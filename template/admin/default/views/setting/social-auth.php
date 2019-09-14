<style>
    .checkbox label{width: 130px}
</style>
    <form method="post">
        <h3><?=$_->l('Выберите, какие социальные сети использовать для авторизации:')?></h3>
        <input type="hidden" name="set_networks" value="1">
        <div class="checkbox">
            <label>
                <input type="checkbox" <?= (in_array('google', $socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="google"> <?=$_->l('google')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('facebook',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="facebook"> <?=$_->l('facebook')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('twitter',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="twitter"> <?=$_->l('twitter')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('vkontakte',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="vkontakte"> <?=$_->l('vkontakte')?>
            </label>
            <br>
            <label>
                <input type="checkbox" <?= (in_array('mailru',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="mailru"> <?=$_->l('mailru')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('odnoklassniki',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="odnoklassniki"> <?=$_->l('odnoklassniki')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('yandex',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="yandex"> <?=$_->l('yandex')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('instagram',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="instagram"> <?=$_->l('instagram')?>
            </label>
            <br>
            <label>
                <input type="checkbox" <?= (in_array('openid',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="openid"> <?=$_->l('openid')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('lastfm',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="lastfm"> <?=$_->l('lastfm')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('linkedin',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="linkedin"> <?=$_->l('linkedin')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('liveid',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="liveid"> <?=$_->l('liveid')?>
            </label>
            <br>
            <label>
                <input type="checkbox" <?= (in_array('soundcloud',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="soundcloud"> <?=$_->l('soundcloud')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('steam',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="steam"> <?=$_->l('steam')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('uid',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="uid"> <?=$_->l('uid')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('youtube',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="youtube"> <?=$_->l('youtube')?>
            </label>
            <br>
            <label>
                <input type="checkbox" <?= (in_array('webmoney',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="webmoney"> <?=$_->l('webmoney')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('foursquare',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="foursquare"> <?=$_->l('foursquare')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('tumblr',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="tumblr"> <?=$_->l('tumblr')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('googleplus',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="googleplus"> <?=$_->l('googleplus')?>
            </label>
            <br>
            <label>
                <input type="checkbox" <?= (in_array('vimeo',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="vimeo"> <?=$_->l('vimeo')?>
            </label>
            <label>
                <input type="checkbox" <?= (in_array('wargaming',$socialConfig->networks) ? 'checked="checked"' : '') ?>
                       name="networks[]" value="wargaming"> <?=$_->l('wargaming')?>
            </label>
        </div>
        <button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?></button>
    </form>
