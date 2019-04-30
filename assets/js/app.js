require('../css/app.css');
const $ = require('jquery');
require('bootstrap');

$('.custom-file-input').on('change', function () {
    let fileName = $(this).val().split("\\").pop();
    $(this).next('.custom-file-label').html(fileName);
});

$('.rating-up').on('click', function (e) {
    e.preventDefault();
    ratingChange(this);
});

$('.rating-down').on('click', function (e) {
    e.preventDefault();
    ratingChange(this, false);
});

function ratingChange(element, up = true) {
    let rating = $(element).parent('.rating');
    let ratingElement = rating.find('.rating-value').first();
    let ratingState = ratingElement.data('rating-state');
    let eventId = ratingElement.data('event-id');

    if ((ratingState === -1 && !up) || (ratingState === 1 && up)) {
        ratingState = 0;
    } else {
        ratingState = up ? 1 : -1;
    }

    $.ajax({
        type: 'post',
        url: `/api/rating/${eventId}?state=${ratingState}`,
        dataType: 'json',
        success: function (data) {
            let btnUp = rating.find('.rating-up').first();
            let btnDown = rating.find('.rating-down').first();

            btnUp.addClass('disabled');
            btnDown.addClass('disabled');

            if (ratingState > 0) {
                btnUp.removeClass('disabled');
            }
            if (ratingState < 0) {
                btnDown.removeClass('disabled');
            }

            ratingElement.data('rating-state', ratingState);
            ratingElement.text(data.rating);
        },
    });
}
