<?php if ($error == 'no_emp') {
    ?>
<div class="main c">
     <div class="for-clients">
      <h2>Эта страница доступна только для работодателей</h2>
        <p><a href="/registration/?type=empl">Зарегистрируйтесь</a> или <a href="/login/">авторизуйтесь</a> под аккаунтом работодателя.</p>
        <span>Если у вас возникли вопросы - обратитесь к нашему онлайн-консультанту или в <a href="https://feedback.fl.ru/">Службу поддержки</a>. С удовольствием ответим.</span>
     </div>
</div>
<?php 
} else {
    if ($error == 'contest_closed') {
        ?>
<div class="main c">
     <div class="for-clients">
      <h2>Конкурс завершен</h2>
        <p>Вы не можете редактировать завершенный конкурс.</p>
        <span>Если у вас возникли вопросы - обратитесь к нашему онлайн-консультанту или в <a href="https://feedback.fl.ru/">Службу поддержки</a>. С удовольствием ответим.</span>
     </div>
</div>
<?php 
    }
} ?>
