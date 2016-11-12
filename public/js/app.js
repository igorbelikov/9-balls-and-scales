// 9 balls and scales
(function () {
    "use strict";

    var balls = [],
        step = 1,
        gameId = null;

    /**
     * @param text
     */
    function notify(text) {
        $.notiny({
            text: text,
            position: 'right-top',
            theme: 'light'
        });
    }

    /**
     * @param action
     * @param params
     * @param callback
     */
    function sendRequest(action, params, callback) {
        params = params || {};
        params.action = action;
        $.post('bootstrap.php', params, function (response) {
            if (response.success) {
                callback.call(this, response.data, response)
            } else {
                notify('Error!');
            }
        }, 'json');
    }

    /**
     * @param ball
     * @param index
     * @param list
     * @returns {Array}
     */
    function createBall(ball, index, list) {
        list = list || balls;
        var $ball = $('<div class="ball">');
        if (ball.isHeavy) {
            $ball.addClass('ball--heavy');
        }
        ball.index = index;
        list.push({
            $el: $ball,
            ball: ball
        });
        return list;
    }

    /**
     * @param $container
     * @param list
     */
    function draw($container, list) {
        list = list || balls;
        var $balls = $container.find('.balls').empty();
        $(list).each(function (index, ball) {
            ball.$el.text(typeof ball.ball.index === "undefined" ? index : ball.ball.index);
            $balls.append(ball.$el.clone());
        });
    }

    /**
     * @param list
     */
    function refreshBalls(list) {
        balls = [];
        $(list).each(function (index, ball) {
            createBall(ball, typeof ball.index === "undefined" ? index : ball.index);
        });
    }

    /**
     * @param ballsList
     */
    function prepareColors(ballsList) {
        $(ballsList).each(function (index, ball) {
            if (ball.ball.index < 3) {
                ball.$el.addClass('ball--group-1');
            } else if (ball.ball.index < 6) {
                ball.$el.addClass('ball--group-2');
            }
        });
    }

    /**
     * @param callback
     * @param replay
     */
    function nextStep(callback, replay) {
        replay = replay || 0;
        if (replay) {
            step = 1;
        }

        var $steps = $('.step').removeClass('step--current'),
            $currentStep = $('.step-' + step).addClass('step--current'),
            $stepResult = $currentStep.find('.step__result');

        if (step > 1) {
            $currentStep.fadeIn();
            draw($currentStep);
            $('.steps')
                .removeClass('steps--step-' + (step - 1))
                .addClass('steps--step-' + step);
        }

        switch (step++) {
            case 1:
                $steps.fadeOut().promise().done(function () {
                    $('#start').prop('disabled', true);
                    $('#replay').prop('disabled', false);
                    $('#next').prop('disabled', false);
                    sendRequest('start', {replay: replay}, function (data) {
                        $('.app__notify--start').fadeOut(function () {
                            $('.step-1').fadeIn(function () {
                                if (typeof callback !== "undefined") {
                                    callback.call(this, data);
                                }
                            });
                            gameId = data.gameId;
                            refreshBalls(data.balls);
                            draw($currentStep);
                        });
                    });
                });
                break;

            case 3:
                sendRequest('weigh', {
                    balls1: JSON.stringify(balls.slice(0, 3).map(function (ball) {
                        return ball.ball;
                    })),
                    balls2: JSON.stringify(balls.slice(3, 6).map(function (ball) {
                        return ball.ball;
                    })),
                }, function (data) {
                    if (data.balls.length === 0) {
                        $stepResult.find('.variant-equal').fadeIn();
                        refreshBalls(balls.slice(6, 9).map(function (ball) {
                            return ball.ball;
                        }));
                    } else {
                        refreshBalls(data.balls);
                        prepareColors(balls);
                        draw($stepResult);
                        $stepResult.find('.variant-balls').fadeIn();
                    }
                    $(balls).each(function (index, ball) {
                        if (index > 1) {
                            ball.$el.addClass('ball--group-3');
                        }
                    });
                });
                $currentStep.find('.ball').each(function (index, el) {
                    if (index > 5) {
                        $(this).addClass('ball--group-3');
                    }
                });

            case 2:
                $currentStep.find('.ball').each(function (index, el) {
                    if (index < 3) {
                        $(this).addClass('ball--group-1');
                    } else if (index < 6) {
                        $(this).addClass('ball--group-2');
                    }
                });
                break;

            case 4:
                sendRequest('weigh', {
                    balls1: JSON.stringify([balls[0].ball]),
                    balls2: JSON.stringify([balls[1].ball])
                }, function (data) {
                    if (data.balls.length === 0) {
                        $stepResult.find('.variant-equal').fadeIn();
                        balls = createBall(balls[2].ball, balls[2].ball.index, []);
                        prepareColors(balls);
                        $('.js__heavy-ball').text(tBall.ball.index);
                    } else {
                        $stepResult.find('.variant-balls').fadeIn();
                        balls = [];
                        createBall(data.balls[0], data.balls[0].index);
                        prepareColors(balls);
                        draw($stepResult);
                        $('.js__heavy-ball').text(data.balls[0].index);
                    }
                });
                break;

            case 5:
                $('#next').prop('disabled', true);
                break;
        }
    }

    $(function () {
        $('.balls').on('click', '.ball', function () {
            if ($(this).hasClass('ball--heavy') || $('.step-2').is(':visible')) {
                return;
            }

            $(this).closest('.step').find('.ball').removeClass('ball--heavy');
            $(this).addClass('ball--heavy');

            sendRequest('mark-as-heavy', {
                gameId: gameId,
                index: parseInt($(this).text())
            }, function (data) {
                notify('Action "' + data.actionLabel + '" is saved.');
                refreshBalls(data.balls);
            });
        });

        $('#replay').click(function () {
            nextStep(function (data) {
                $('.variant').fadeOut();
                notify('Action "' + data.actionLabel + '" is saved.<br>Game #' + data.gameId);
            }, true);
        });

        $('#start').click(function () {
            nextStep(function (data) {
                notify('Action "' + data.actionLabel + '" is saved.<br>Game #' + data.gameId);
            });
        });

        $('#next').click(function () {
            nextStep();
        });
    });
})();