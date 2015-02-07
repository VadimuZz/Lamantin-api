/*!
 * Lamantin-api JavaScript Library v1.0
 * Author Vadim Kudriavtcev [VadimuZ]
 * Requered jquery http://jquery.com/
 *
 * Copyright 2015
 * Released under the MIT license
 *
 * Date: 2015-02-06T15:03Z
 */

function Lamantin() {
	this.apiurl;
	this.banned = undefined;
	this.errors = undefined;
	this.notify = undefined;
	this.update = undefined;
	this.common = {
		auth_key: "none",
	};
    this.setSendData = function(method, data) {
		return { "common": this.common, "method": method, "data": data }
	}
	this.sendRequest = function(method, data, callBack) {
		if(!data) {
			data = { }
		}
		$.post(this.apiurl, this.setSendData(method, data), function(response) {
			response = jQuery.parseJSON(response);
			// Banned
			if(response["ban"] !== undefined && this.banned !== undefined) {
				this.banned(response["ban"]);
				return;
			}
			// Errors
			if(response["error"] !== undefined && this.errors !== undefined) {
				this.errors(response["error"]);
				return;
			} 
			// Notifications
			if(response["note"] !== undefined && this.banned !== undefined) {
				this.banned(response["note"]);
			}
			// Update
			if(response["data"] !== undefined) {
				this.update(response);
			}
			// Callback
			if (callBack !== undefined) {
				callBack(response);
			}
		});
	}
}