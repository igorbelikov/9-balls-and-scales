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

   function clearBalls() {
      balls = [];
      $('.balls').empty();
   }

   $(function () {
      $('.balls').on('click', '.ball', function () {
         if ($(this).hasClass('ball--heavy')) {
            return;
         }
         $('.ball').removeClass('ball--heavy');
         $(this).addClass('ball--heavy');
         $.post('bootstrap.php', {action: 'mark-as-heavy', index: $(this).text()}, function (response) {

         }, 'json');
      });

      $('#replay').click(function () {
         $.post('bootstrap.php', {action: 'replay'}, function (response) {
            clearBalls();
            $(response.data.balls).each(function (index, ball) {
               createBall(ball);
            });
            draw(balls);
         });
      });

      $('#start').click(function () {
         $(this).prop('disabled', true);
         $('#replay').prop('disabled', false);
         $.post('bootstrap.php', {action: 'start'}, function (response) {
            $('.app__notify--start').fadeOut(function () {
               $('.step-1').fadeIn();
               $(response.data.balls).each(function (index, ball) {
                  createBall(ball);
               });
               draw(balls);
            });
         }, 'json');
      });
   });
})();