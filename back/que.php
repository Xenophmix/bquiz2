<fieldset>
  <legend>新增問卷</legend>

  <form action="./api/add_que.php" method="POST">

    <div class="subject">
      <label>問卷名稱</label>
      <input type="text" name="subject" id="">
    </div>
    <div class="options">
      <div>
        <label>選項</label>
        <input type="text" name="option[]" id="">
      </div>
    </div>
    <input type="submit" value="新增">
    <input type="reset" value="清空">
    <input type="button" value="更多" onclick="moreOpt()">
  </form>
</fieldset>

<script>
  function moreOpt() {
    if ($('.options div').length <= 5) {
      console.log($('.options div').length)
      let opt =
        `
      <div>
      <label>選項</label>
      <input type="text" name="option[]" id="">
      </div>
      `
      $(".options").append(opt);
    }
  }
</script>