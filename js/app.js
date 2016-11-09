(function () {
   "use strict";

   var balls = [];

   function createBall(ball) {
      var $ball = $('<div class="ball">');
      if (ball.isHeavy) {
         $ball.addClass('ball--heavy');
      }
      balls.push({
         $el: $ball,
         ball: ball
      });
   }

   function draw(balls) {
      $(balls).each(function (index, ball) {
         ball.$el.text(index);
         $('.balls').append(ball.$el);
      });
   }

   $(function () {
      $('#start').click(function () {
         $.post('bootstrap.php', {action: 'start'}, function (response) {
            $('.app__notify--start').fadeOut(function () {
               $(response.data.balls).each(function (index, ball) {
                  createBall(ball);
               });
               draw(balls);
            });
         }, 'json');
      });
   });
})();