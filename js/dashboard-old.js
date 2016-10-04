var htmlTemplates = {};
var channelStatus = {};
var messageDetails = {};



$(document).ready(function () {
	compileTemplates();

	
	updateMessageList();

	// get nexmo balance
	$.getJSON( "rest/status.php?action=balance&provider=NEXMO", function( balance ) {
		channelStatus.nexmo_balance = balance;
		$("#headBalanceNexmo").html(channelStatus.nexmo_balance);
	});

	// get clickatel balance
	$.getJSON( "rest/status.php?action=balance&provider=CLICKATELL", function( balance ) {
		channelStatus.clickatel_balance = balance;
		$("#headBalanceClickatel").html(channelStatus.clickatel_balance);
	});


});


function compileTemplates () {
	$("script[type='text/x-handlebars-template']").each(function(elem) {
		htmlTemplates[this.id] = Handlebars.compile($(this).html());
	});
}


function updateBalance() {
	$("#headBalanceNexmo").html(channelStatus.nexmo_balance);
	$("#headBalanceClickatel").html(channelStatus.clickatel_balance);
}


function updateMessageList() {
	$("#messageTableBody").html("");

	// get messages
	$.getJSON( "rest/status.php?action=messages", function( messages ) {
		channelStatus.lastMessages = messages;
		for(var message of channelStatus.lastMessages) {
			message.hasErrors = message.statusCd != "0" || message.statusCd != 0;
			message.channelLogoUrl = "images/" + message.channel.toLowerCase() + ".png";
			$("#messageTableBody").append(htmlTemplates.messageTableRow(message));
		}
	});


	
}

function updateMessageDetails(entryId) {

	var messsageIndex = findIndexByKey(channelStatus.lastMessages, "entryId", entryId);
	if(messsageIndex >= 0) {
		$("#messageDetailsModalBody").html(htmlTemplates.messageDetailsModalContent(channelStatus.lastMessages[messsageIndex]));
		$('#messageDetailsModal').modal('show');

		var thisMessage = channelStatus.lastMessages[messsageIndex];

		var messageId = thisMessage.messageId;
		var channel = thisMessage.channel;
		var provider = thisMessage.provider;
		if(messageId != "") {
			$("#messageDetailsChannelData").val("loading...");
			// get message details
			$.getJSON( "rest/status.php?action=message&channel=" + channel + "&provider=" + provider + "&message=" + messageId, function( messageDetailsObj ) {
				var messageDetailsText = "";

				// 
				for(item in messageDetailsObj.messageDetails) {
					if(typeof item != "object") {
						messageDetailsText += item + ": " + messageDetailsObj.messageDetails[item] + "\r\n";
					}
						
				}
				$("#messageDetailsChannelData").val(messageDetailsText);
				
			});
		}
	}
}

function updateProviderList(elem) {
	var channel = $(elem).val();
	if(channel != null && channel != "") {
		channelClass = channel.toLowerCase();
		$("select[name='testMessageProvider']").val("");
		$("option.provider").hide();
		$("option.provider." + channelClass).show();
		$(".telegram").hide();
		$(".slack").hide();
		$("."+channelClass).show();
	}
}

function displayTestMessagePopup(element) {
	var channel = $(element).attr("name");
	console.log("attr name: ", channel);
	$("select[name='testMessageChannel']").val(channel);
	$("select[name='testMessageProvider']").val("");
	$("option.provider").hide();
	$(".telegram").hide();

	if(channel != null && channel != "") {
		var channelClass = channel.toLowerCase();		
		$("option.provider").hide();
		$("option.provider." + channelClass).show();
		$(".telegram").hide();
		$(".slack").hide();
		$("."+channelClass).show();
	} 
}


function sendTestMessage() {
	var channel = $("select[name='testMessageChannel']").val();
	var provider = $("select[name='testMessageProvider']").val();
	var sender = $("input[name='testMessageSender']").val();
	var recipient = $("input[name='testMessageRecipient']").val();
	var subject = $("input[name='testMessageSubject']").val();
	var body = $("textarea[name='testMessageBody']").val();
	var dataCsv = $("input[name='testMessageBodyDataCSV']").val();

	var sampleMessageRequest = {subject: subject, sender: sender, recipient: recipient, body: body, channel:channel, provider: provider, bodyVariablesCSV: dataCsv};

	$.ajax({url: "rest/", method: "POST", data: sampleMessageRequest}).done(function(result) {
		
		$('#messageTestModal').modal('hide');
		if(result.errorText != "") {
			alert(result.errorText);
		}

		// refresh messages list
		// get messages
		updateMessageList();

	});
}


function findIndexByKey(list, key, value) {
	return list.findIndex(function (element) { return element[key] === value; });
}

function getTelegramService() {
	var action = "getUpdates";

	var requestData = {action: action};

	$.ajax({url: "rest/telegram_service.php", method: "POST", data: requestData}).done(function(result) {
				
		if(result.message != "") {
			alert(result.message);
			$("input[name='testMessageRecipient']").val(result.chat_id);
		}

	});
}

