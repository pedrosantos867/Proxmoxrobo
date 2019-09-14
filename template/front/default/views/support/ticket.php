<div class="ajax-block">
    <style type="text/css">

        h4 {
            float: right;
            color: grey;
        }

        h3 {
            margin: 0 0 20px;
            padding: 15px 150px 10px 0;
            font-size: 28px;
            border-bottom: 2px solid #8C8C8C;
            position: relative;
        }

        h3:after {
            content: "";
            position: absolute;
            height: 2px;
            width: 30%;
            background-color: orange;
            bottom: -2px;
            left: 0;
        }

        h3 span {
            font-weight: 300;
            font-style: italic;
            display: inline-block;
            margin-right: 5px;
            color: grey;
            font-size: 24px;
        }

        .btn {
            cursor: default;
            margin: 0 15px 0 0;
        }

        .ticket_status-buttons {
            margin-bottom: 20px;
        }

        .ticket_message {
            margin-bottom: 10px;
        }

        .ticket_message p:first-child {
            font-size: 18px;
            margin-bottom: 3px;
        }

        .ticket_message p:last-child {
            padding: 10px;
            border: 1px solid #CDCDCD;
            border-radius: 10px;
            background-color: rgb(240, 240, 240);
        }

        h5 {
            font-style: italic;
        }

        .thumbnail {
            padding: 0px;
        }

        .panel {
            position: relative;
        }

        .panel > .panel-heading:after, .panel > .panel-heading:before {
            position: absolute;
            top: 11px;
            left: -16px;
            right: 100%;
            width: 0;
            height: 0;
            display: block;
            content: " ";
            border-color: transparent;
            border-style: solid solid outset;
            pointer-events: none;
        }

        .panel > .panel-heading:after {
            border-width: 7px;
            border-right-color: #f7f7f7;
            margin-top: 1px;
            margin-left: 2px;
        }

        .panel > .panel-heading:before {
            border-right-color: #ddd;
            border-width: 8px;
        }

        .animated {
            -webkit-transition: height 0.2s;
            -moz-transition: height 0.2s;
            transition: height 0.2s;
        }

        .stars {
            margin: 20px 0;
            font-size: 24px;
            color: #d17581;
        }
        .ts_hint {
            display: inline-block;
            vertical-align: middle;
            margin: 0 5px;
            font-weight: 700;
        }
    </style>
    <script>
        // (function(e){var t,o={className:"autosizejs",append:"",callback:!1,resizeDelay:10},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',n=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent"],s=e(i).data("autosize",!0)[0];s.style.lineHeight="99px","99px"===e(s).css("lineHeight")&&n.push("lineHeight"),s.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),s.parentNode!==document.body&&e(document.body).append(s),this.each(function(){function o(){var t,o;"getComputedStyle"in window?(t=window.getComputedStyle(u,null),o=u.getBoundingClientRect().width,e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){o-=parseInt(t[i],10)}),s.style.width=o+"px"):s.style.width=Math.max(p.width(),0)+"px"}function a(){var a={};if(t=u,s.className=i.className,d=parseInt(p.css("maxHeight"),10),e.each(n,function(e,t){a[t]=p.css(t)}),e(s).css(a),o(),window.chrome){var r=u.style.width;u.style.width="0px",u.offsetWidth,u.style.width=r}}function r(){var e,n;t!==u?a():o(),s.value=u.value+i.append,s.style.overflowY=u.style.overflowY,n=parseInt(u.style.height,10),s.scrollTop=0,s.scrollTop=9e4,e=s.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=w,n!==e&&(u.style.height=e+"px",f&&i.callback.call(u,u))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==g&&(g=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),w=0,f=e.isFunction(i.callback),z={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},g=p.width();p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(w=p.outerHeight()-p.height()),c=Math.max(parseInt(p.css("minHeight"),10)-w||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word",resize:"none"===p.css("resize")||"vertical"===p.css("resize")?"none":"horizontal"}),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(z).removeData("autosize")}),r())})):this}})(window.jQuery||window.$);

        //var __slice=[].slice;(function(e,t){var n;n=function(){function t(t,n){var r,i,s,o=this;this.options=e.extend({},this.defaults,n);this.$el=t;s=this.defaults;for(r in s){i=s[r];if(this.$el.data(r)!=null){this.options[r]=this.$el.data(r)}}this.createStars();this.syncRating();this.$el.on("mouseover.starrr","span",function(e){return o.syncRating(o.$el.find("span").index(e.currentTarget)+1)});this.$el.on("mouseout.starrr",function(){return o.syncRating()});this.$el.on("click.starrr","span",function(e){return o.setRating(o.$el.find("span").index(e.currentTarget)+1)});this.$el.on("starrr:change",this.options.change)}t.prototype.defaults={rating:void 0,numStars:5,change:function(e,t){}};t.prototype.createStars=function(){var e,t,n;n=[];for(e=1,t=this.options.numStars;1<=t?e<=t:e>=t;1<=t?e++:e--){n.push(this.$el.append("<span class='glyphicon .glyphicon-star-empty'></span>"))}return n};t.prototype.setRating=function(e){if(this.options.rating===e){e=void 0}this.options.rating=e;this.syncRating();return this.$el.trigger("starrr:change",e)};t.prototype.syncRating=function(e){var t,n,r,i;e||(e=this.options.rating);if(e){for(t=n=0,i=e-1;0<=i?n<=i:n>=i;t=0<=i?++n:--n){this.$el.find("span").eq(t).removeClass("glyphicon-star-empty").addClass("glyphicon-star")}}if(e&&e<5){for(t=r=e;e<=4?r<=4:r>=4;t=e<=4?++r:--r){this.$el.find("span").eq(t).removeClass("glyphicon-star").addClass("glyphicon-star-empty")}}if(!e){return this.$el.find("span").removeClass("glyphicon-star").addClass("glyphicon-star-empty")}};return t}();return e.fn.extend({starrr:function(){var t,r;r=arguments[0],t=2<=arguments.length?__slice.call(arguments,1):[];return this.each(function(){var i;i=e(this).data("star-rating");if(!i){e(this).data("star-rating",i=new n(e(this),r))}if(typeof r==="string"){return i[r].apply(i,t)}})}})})(window.jQuery,window);$(function(){return $(".starrr").starrr()})

        $(function () {

            // $('#new-review').autosize({append: "\n"});

            var reviewBox = $('#post-review-box');

            var openReviewBtn = $('#open-review-box');
            var closeReviewBtn = $('#close-review-box');


            openReviewBtn.click(function (e) {
                reviewBox.slideDown(400, function () {
                    $('#new-review').trigger('autosize.resize');
                    // newReview.focus();
                });
                openReviewBtn.fadeOut(100);
                closeReviewBtn.show();
            });

            closeReviewBtn.click(function (e) {
                e.preventDefault();
                reviewBox.slideUp(300, function () {
                    //  newReview.focus();
                    openReviewBtn.fadeIn(200);
                });
                closeReviewBtn.hide();

            });


        });
    </script>
    <h4><?= $_->l('Тикет') ?> №: <?= $ticket->id ?></h4>
    <h3><span><?= $_->l('Тема') ?>:</span> <?= $ticket->subject ?></h3>

    <p class="ticket_status-buttons">
        <span class="ts_hint"><?= $_->l('Статус') ?>: </span>


        <? if ($ticket->status == -1) { ?>
            <span class="label label-success"><?= $_->l('Новый') ?></span>
        <? } elseif ($ticket->status == 0) { ?>
            <span class="label label-warning"><?= $_->l('В обработке') ?></span>
        <? } else { ?>
            <span class="label label-danger"><?= $_->l('Закрыт') ?></span>
        <? } ?>

        <span class="ts_hint"><?= $_->l('Приоритет') ?>: </span>
        <? if ($ticket->priority == 0) { ?>
            <span class="label label-danger"><?= $_->l('Низкий') ?></span>
        <? } elseif ($ticket->priority == 1) { ?>
            <span class="label label-warning"><?= $_->l('Средний') ?></span>
        <? } elseif ($ticket->priority == 2) { ?>
            <span class="label label-success"><?= $_->l('Высокий') ?></span>
        <? } ?>
        <span style="vertical-align: middle; float: right; font-weight: 700;"><?= $ticket->date ?></span>
    </p>

    <div class="ticket_message">
        <p><?= $_->l('Сообщение') ?>: </p>

        <p><?= $ticket->message ?></p>
    </div>
    <div>
        <? foreach ($ticket->files as $file) { ?>
            <span class="glyphicon glyphicon-paperclip"></span>  <a
                href="<?= $_->link('support/download/file/' . $ticket->id . '/' . $file) ?>"><?= $file ?></a> <br>
        <? } ?>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h3><?= $_->l('Ответы') ?></h3>
            </div>
            <!-- /col-sm-12 -->
        </div>
        <!-- /row -->
        <? foreach ($answers as $answer) { ?>
            <div class="row">
                <div class="col-sm-1">
                    <div class="thumbnail">
                        <img class="img-responsive user-photo" src="https://ssl.gstatic.com/accounts/ui/avatar_2x.png">
                    </div>
                    <!-- /thumbnail -->
                </div>
                <!-- /col-sm-1 -->
                <div class="col-sm-11">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?
                            $types = array(
                                's' => $_->l('секунда|секунды|секунд'),
                                'min' => $_->l('минуту|минуты|минут'),
                                'h' => $_->l('час|часа|часов'),
                                'd' => $_->l('день|дня|дней'),
                                'm' => $_->l('месяц|месяца|месяцов'),
                                'y' => $_->l('год|года|год')
                            );
                            $w = $types[$answer->days->word];
                            ?>
                            <strong><?= $answer->admin ? '<span style="color: mediumpurple">' . $answer->author->name . '</span>' : $answer->author->name ?></strong> <span
                                class="text-muted"><?= $_->l('опубликовал %period {%period|%word} назад', array('period' => $answer->days->day, 'word' => $w)) ?>
                            </span>
                        </div>
                        <div class="panel-body">
                            <?= $answer->answer ?>
                            <div>
                                <? foreach ($answer->files as $file) { ?>
                                    <span class="glyphicon glyphicon-paperclip"></span>   <a
                                        href="<?= $_->link('support/download/answer/file/' . $answer->id . '/' . $file) ?>"><?= $file ?></a>
                                    <br>
                                <? } ?>
                            </div>
                        </div>
                        <!-- /panel-body -->
                    </div>
                    <!-- /panel panel-default -->
                </div>
                <!-- /col-sm-5 -->
            </div><!-- /container -->
        <? } ?>
        <? if ($ticket->status != 1){ ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="well well-sm">
                        <div class="text-right">
                            <a class="btn btn-success btn-green" href="#reviews-anchor"
                               id="open-review-box"><?= $_->l('Ответить') ?></a>
                        </div>

                        <div class="row" id="post-review-box" style="display:none;">
                            <div class="col-md-12">
                                <form action="" method="post" enctype="multipart/form-data">

                            <textarea class="form-control animated" cols="50" id="new-review" name="comment"
                                      placeholder="<?=$_->l('Напишите свой ответ здесь...')?>" rows="5"></textarea>
                                    <input type="file" multiple="multiple" name="files[]" class="form-control"
                                           style="margin-top: 5px">
                                    <div class="text-right" style="margin-top: 10px">

                                        <a class="btn btn-danger btn-sm" href="#" id="close-review-box"
                                           style="display:none; margin-right: 10px;">
                                            <span class="glyphicon glyphicon-remove"></span><?= $_->l('Отмена') ?></a>
                                        <button class="btn btn-success btn-sm" type="submit"><?= $_->l('Отправить') ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        <? } ?>
    </div>

</div>
