var Checker = {
    itemCount: undefined,
    url: undefined,
    interval: undefined,
    additionalData: undefined,
    initialize: function () {
        var that = this;
        $.ajax({
            url: that.url,
            method: 'post',
            dataType: 'json',
            data: {
                ajax: 1,
                init: true,
                data: that.additionalData
            },
            success: function (data) {
                that.itemCount = data.count;
                console.info("Checker successful connect to server");
                that.getCount();
            },
            fail: function () {
                console.error("Checker can't connect to server");
            }
        });
    },
    getCount: function () {
        var that = this;
        $.ajax({
            url: that.url,
            method: 'post',
            dataType: 'json',
            data: {
                ajax: 1,
                count: that.itemCount,
                data: that.additionalData
            },
            success: function (data) {
                if((that.itemCount < data.count) && that.onSignalize != undefined) {
                    that.itemCount = data.count;
                    data.items.forEach(function (item) {
                        that.signalize(item, data);
                    });
                }
                if (that.afterStep != undefined) that.afterStep(data);
                setTimeout(function(){that.getCount()}, that.interval);
            }
        });
    },
    afterStep: undefined,
    onSignalize: undefined,
    signalize: function(item, data) {this.onSignalize(item, data, this)},
    start: function (url, onSignalize, interval, additionalData) {
        this.url = url;
        this.onSignalize = onSignalize;
        if(interval != undefined) this.interval = interval;
        else this.interval = 8000;
        if(additionalData != undefined) this.additionalData = additionalData;
        this.initialize();
    }
};

