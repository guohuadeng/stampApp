var Dailydeal = Class.create();

Dailydeal.prototype = {
    initialize: function (changeProductUrl) {
        this.changeProductUrl = changeProductUrl;
    },
    changeProduct: function (product_id) {
        var url = this.changeProductUrl;
        if (url.indexOf('?') == -1)
            url += '?product_id=' + product_id;
        else
            url += '&product_id=' + product_id;
        new Ajax.Updater('product_name_contain', url, {
            method: 'get', onComplete: function () {
                updateProductName();
            }, onFailure: ""
        });
    },
    checkBidderName: function () {
        var bidder_name = $('bidder_name').value;
        $('bidder_name_form_button').hide();

        var url = this.changeProductUrl;
        if (url.indexOf('?') == -1)
            url += '?bidder_name=' + bidder_name;
        else
            url += '&bidder_name=' + bidder_name;

        new Ajax.Updater('bidder-notice', url, {
            method: 'get', onComplete: function () {
                validBidderName();
            }, onFailure: ""
        });

        $('biddername-please-wait').style.display = "block";
        $('bidder-notice').style.display = "none";
        $('bidder_name').removeClassName('validation-passed');
    }
}

var DailydealTimeCounter = Class.create();
DailydealTimeCounter.prototype = {
    //params now_time, end_time : seconds
    initialize: function (now_time, end_time, dailydeal_id) {
        this.now_time = parseInt(now_time) * 1000;
        this.end_time = parseInt(end_time) * 1000;
        this.dailydeal_id = dailydeal_id;
        this.end = new Date(this.end_time);
        var endDate = this.end;
        this.second = endDate.getSeconds();
        this.minute = endDate.getMinutes();
        this.hour = endDate.getHours();
        this.day = endDate.getDate();
        this.month = endDate.getMonth();
        var yr;
        if (endDate.getYear() < 1900)
            yr = endDate.getYear() + 1900;
        else
            yr = endDate.getYear();
        this.year = yr;
    },

    setTimeleft: function (timeleft_id) {
        var now = new Date(this.now_time);
        var yr;

        if (now.getYear() < 1900)
            yr = now.getYear() + 1900;
        else
            yr = now.getYear();

        var endtext = '0';
        var timerID;

        var sec = this.second - now.getSeconds();

        var min = this.minute - now.getMinutes();
        var hr = this.hour - now.getHours();
        var dy = this.day - now.getDate();
        var mnth = this.month - now.getMonth();
        yr = this.year - yr;

        var daysinmnth = 32 - new Date(now.getYear(), now.getMonth(), 32).getDate();
        if (sec < 0) {
            sec = (sec + 60) % 60;
            min--;
        }
        if (min < 0) {
            min = (min + 60) % 60;
            hr--;
        }
        if (hr < 0) {
            hr = (hr + 24) % 24;
            dy--;
        }
        if (dy < 0) {
            dy = (dy + daysinmnth) % daysinmnth;
            mnth--;
        }
        if (mnth < 0) {
            mnth = (mnth + 12) % 12;
            yr--;
        }
        var sectext = "sec";
        var mintext = "min";
        var hrtext = "hour";
        var dytext = " days ";
        var mnthtext = " months ";
        var yrtext = " years ";
        if (yr == 1)
            yrtext = " year ";
        if (mnth == 1)
            mnthtext = " month ";
        if (dy == 1)
            dytext = " day ";
        if (hr == 1)
            hrtext = "hour";
        if (min == 1)
            mintext = "min";
        if (sec == 1)
            sectext = "sec";

        if (dy < 10)
            dy = '0' + dy;
        if (hr < 10)
            hr = '0' + hr;
        if (min < 10)
            min = '0' + min;
        if (sec < 10)
            sec = '0' + sec;

        if (yr <= 0)
            yrtext = ''
        else
            yrtext = '<li class="time-year"><div><span class="timeleft-text">' + yr + '</span><span class="text-time">' + yrtext + '</span></div></li>';

        if ((mnth <= 0))
            mnthtext = ''
        else
            mnthtext = '<li class="time-month"><div><span class="timeleft-text">' + mnth + '</span><span class="text-time">' + mnthtext + '</span></div></li>';

        if (dy <= 0 && mnth > 0)
            dytext = ''
        else
            dytext = '<li class="time-day"><div><span class="timeleft-text">' + dy + '</span><span class="text-time">' + dytext + '</span></div></li>';

        if (hr <= 0 && dy > 0)
            hrtext = ''
        else
            hrtext = '<li class="time-hr"><div><span class="timeleft-text">' + hr + '</span><span class="text-time">' + hrtext + '</span></div></li>';

        if (min < 0)
            mintext = ''
        else
            mintext = '<li class="time-min"><div><span class="timeleft-text">' + min + '</span><span class="text-time">' + mintext + '</span></div></li>';

        if (sec < 0)
            sectext = ''
        else
            sectext = '<li class="time-sec"><div><span class="timeleft-text">' + sec + '</span><span class="text-time">' + sectext + '</span></div></li>';

        if (now >= this.end) {
            jQuery("."+timeleft_id).html(endtext);
            clearTimeout(timerID);
        }
        else {
            jQuery("."+timeleft_id).html(yrtext + mnthtext + dytext + hrtext + mintext + sectext);
        }

        if (this.now_time == this.end_time) {
            location.reload(true);
            return;
        }

        this.now_time = this.now_time + 1000;

        timerID = setTimeout("setDailydealTimeleft(" + (this.now_time / 1000) + "," + (this.end_time / 1000) + ",'" + timeleft_id + "','" + this.dailydeal_id + "');", 1000);
    }
}

var timerCounters = {};
function setDailydealTimeleft(now_time, end_time, timeleft_id, dailydeal_id) {
    if (timerCounters[dailydeal_id] == undefined) {
        timerCounters[dailydeal_id] = new DailydealTimeCounter(now_time, end_time, dailydeal_id);
    } else {
        timerCounters[dailydeal_id].initialize(now_time, end_time, dailydeal_id);
    }
    timerCounters[dailydeal_id].setTimeleft(timeleft_id);
}

function updateProductName() {
    $('product_name').value = $('newproduct_name').value;
    $('dailydeal_tabs_form_section').addClassName('active');
    $('dailydeal_tabs_form_listproduct').removeClassName('active');
    $('dailydeal_tabs_form_section_content').style.display = "";
    $('dailydeal_tabs_form_listproduct_content').style.display = "none";
}


function showSelectProductTab() {
    $('dailydeal_tabs_form_section').removeClassName('active');
    $('dailydeal_tabs_form_listproduct').addClassName('active');
    Element.show('dailydeal_tabs_form_listproduct_content');
    Element.hide('dailydeal_tabs_form_section_content');
}