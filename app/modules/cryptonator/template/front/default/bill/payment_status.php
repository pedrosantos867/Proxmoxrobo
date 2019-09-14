
<script>
    $(function () {
        loader.init();
        loader.display();
        setTimeout(function () {
            location.href = '<?=$_->link("modules/cryptonator/result?bill=$bill->id&show_result=1")?>'
        }, 4000)
    })
</script>
