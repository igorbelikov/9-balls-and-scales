// 9 balls and scales
(function () {
    "use strict";

    var balls = [],
        step = 1,
        gameId = null;

    /**
     * @param action
     * @param params
     * @param callback
     */
    function sendRequest(action, params, callback) {
        params = params || {};
        params.action = action;
        $.post('bootstrap.php', params, callback, 'json');
    }

    function nextStep() {
        if (step > 1) {
            $('.steps')
                .removeClass('steps--step-' + (step - 1))
                .addClass('steps--step-' + step);
            $('.step-' + step).fadeIn();
        }
        switch (step++) {
            case 1:
                $('.step').fadeOut().promise().done(function () {
                    $('#start').prop('disabled', true);
                    $('#replay').prop('disabled', false);
                    $('#next').prop('disabled', false);
                    clearBalls();
                    sendRequest('start', {}, function (response) {
                        $('.app__notify--start').fadeOut(function () {
                            $('.step-1').fadeIn();
                            gameId = response.data.gameId;
                            $(response.data.balls).each(function (index, ball) {
                                createBall(ball);
                            });
                            draw(balls);
                        });
                    });
                });

                break;

            case 5:
                $('#next').prop('disabled', true);
                break;
        }
    }

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
            var $step = $(this).closest('.step');
            if ($(this).hasClass('ball--heavy') || $('.step-2').is(':visible')) {
                return;
            }
            $step.find('.ball').removeClass('ball--heavy');
            $(this).addClass('ball--heavy');
            sendRequest('mark-as-heavy', {gameId: gameId, index: $(this).text()}, function (response) {

            });
        });

        $('#replay').click(function () {
            step = 1;
            nextStep();
        });

        $('#start, #next').click(function () {
            nextStep();
        });
    });
})();