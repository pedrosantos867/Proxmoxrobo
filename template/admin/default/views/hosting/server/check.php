<? use hosting\HostingAPI; ?>
<style>
    .connection_result {
        text-align: center;
        font-size: 24px;
        text-transform: uppercase;
        text-shadow: 0 0 1px #fff, 1px 1px 2px #333;
        position: relative;
    }

    .connection_result:after {
        content: "";
        position: absolute;
        height: 2px;
        width: 80%;
        left: 10%;
        bottom: -10px;
    }

    .connection_result_success:after {
        background-color: #03CC03;
    }

    .connection_result_fail:after {
        background-color: #F70505;
    }
</style>

<div style="text-align: center;">
    <? if ($res == HostingAPI::ANSWER_OK) { ?>
        <span class="connection_result connection_result_success">Соединение установленно!</span>
    <? } elseif ($res == HostingAPI::ANSWER_AUTH_ERROR) { ?>
        <span class="connection_result connection_result_fail">Ошибка авторизации!</span>
    <? } else { ?>
        <span class="connection_result connection_result_fail">Сервер не отвечает!</span>
    <? } ?>
</div>