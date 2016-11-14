// 9 balls and scales
(function () {
    "use strict";

    var draw,
        currentStep = 1;

    /**
     * @param {BallsList} ballsList
     * @constructor
     */
    function Draw(ballsList) {
        this.list = ballsList;
    }

    Draw.prototype.render = function ($container) {
        $container.empty();
        $(this.list.balls).each(function (index, ball) {
            ball.$el.text(ball.index);
            $container.append(ball.$el.clone());
        });
    }

    /**
     * @param isHeavy
     * @param index
     * @constructor
     */
    function Ball(isHeavy, index) {
        this.isHeavy = isHeavy;
        this.index = index;

        this.$el = this.render();
    }

    Ball.prototype.data = function () {
        return {
            isHeavy: this.isHeavy,
            index: this.index
        };
    };

    Ball.prototype.render = function () {
        var $el = $('<div class="ball">');
        if (this.isHeavy) {
            $el.addClass('ball--heavy');
        }
        return $el;
    };

    /**
     * @param {Array} balls
     * @constructor
     */
    function BallsList(balls) {
        this.loadBalls(balls);
    }

    BallsList.prototype.data = function () {
        return this.balls.map(function(ball, index) {
            return ball.data();
        });
    };

    BallsList.prototype.part = function (start, end) {
        return new BallsList(this.balls.slice(start, end));
    }

    BallsList.prototype.loadBalls = function (balls) {
        this.balls = balls.map(function(ball, index) {
            return ball instanceof Ball ? ball : new Ball(ball.isHeavy, typeof ball.index === "undefined" ? index : ball.index);
        });
        return this;
    };

    BallsList.prototype.paint = function () {
        $(this.balls).each(function (index, ball) {
            if (ball.index < 3) {
                ball.$el.addClass('ball--group-1');
            } else if (ball.index < 6) {
                ball.$el.addClass('ball--group-2');
            } else {
                ball.$el.addClass('ball--group-3');
            }
        });
        return this;
    }

    BallsList.prototype.highlight = function (enable) {
        enable = typeof enable === "undefined";
        $(this.balls).each(function (index, ball) {
            if (enable) {
                ball.$el.addClass('ball--highlight');
            } else {
                ball.$el.removeClass('ball--highlight');
            }
        });
        return this;
    }

    /**
     * @param text
     */
    function notify(text) {
        $.notiny({text: text, position: 'right-top', theme: 'light'});
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
     * @param callback
     * @param replay
     */
    function step(callback, replay) {
        replay = replay || 0;

        if (replay) {
            currentStep = 1;
        }

        var $steps = $('.step').removeClass('step--current'),
            $currentStep = $('.step-' + currentStep).addClass('step--current');

        if (currentStep > 1) {
            $currentStep.fadeIn();
            $('.steps')
                .removeClass('steps--step-' + (currentStep - 1))
                .addClass('steps--step-' + currentStep);
        }

        switch (currentStep++) {
            case 1:
                $steps.fadeOut().promise().done(function () {
                    $('#start').prop('disabled', true);
                    $('#replay, #next').prop('disabled', false);
                    sendRequest('start', {replay: replay}, function (data) {
                        $('.app__notify--start').fadeOut(function () {
                            $('.step-1').fadeIn(function () {
                                if (typeof callback !== "undefined") {
                                    callback.call(this, data);
                                }
                            });
                            draw = new Draw(new BallsList(data.balls));
                            draw.render($currentStep.find('.balls'));
                        });
                    });
                });
                break;

            case 2:
                draw.list.part(0, 6).paint();
                draw.render($currentStep.find('.balls'));
                break;

            case 3:
                draw.list.part(6, 9).highlight();
                draw.render($currentStep.find('.step__content .balls'));
                sendRequest('weigh', {
                    balls1: JSON.stringify(draw.list.part(0, 3).data()),
                    balls2: JSON.stringify(draw.list.part(3, 6).data()),
                }, function (data) {
                    if (data.balls.length === 0) {
                        $currentStep.find('.variant-equal').fadeIn();
                        draw.list = draw.list.part(6, 9)
                    } else {
                        draw.list.loadBalls(data.balls).paint();
                        draw.render($currentStep.find('.step__result .balls'));
                        $currentStep.find('.variant-balls').fadeIn();
                    }
                });
                break;

            case 4:
                draw.list.highlight(false).part(2, 3).highlight();
                draw.render($currentStep.find('.step__content .balls'));
                sendRequest('weigh', {
                    balls1: JSON.stringify([draw.list.balls[0].data()]),
                    balls2: JSON.stringify([draw.list.balls[1].data()])
                }, function (data) {
                    if (data.balls.length === 0) {
                        $currentStep.find('.variant-equal').fadeIn();
                        $('.js__heavy-ball').text(draw.list.balls[2].index);
                    } else {
                        draw.list.loadBalls(data.balls).paint();
                        draw.render($currentStep.find('.step__result .balls'));
                        $('.js__heavy-ball').text(data.balls[0].index);
                        $currentStep.find('.variant-balls').fadeIn();
                    }
                });
                break;

            case 5:
                $('#next').prop('disabled', true);
                sendRequest('nextStep', {step: 5}, function (data) {
                    notify('Action "' + data.actionLabel + '" is saved.');
                });
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
                index: parseInt($(this).text())
            }, function (data) {
                notify('Action "' + data.actionLabel + '" is saved.');
                draw.list.loadBalls(data.balls);
            });
        });

        $('#start').click(function () {
            step(function (data) {
                notify('Action "' + data.actionLabel + '" is saved.<br>Game #' + data.gameId);
            });
        });

        $('#replay').click(function () {
            step(function (data) {
                $('.variant').fadeOut();
                notify('Action "' + data.actionLabel + '" is saved.<br>Game #' + data.gameId);
            }, true);
        });

        $('#next').click(function () {
            step();
        });
    });
})();