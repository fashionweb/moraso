$(function() {
    $('.top-bar-section ul').append('<li><a id="menuCartOverviewItem" href="#cart" class="openCartOverview">Warenkorb<span class="qty">0</span></a></li>');
    $('.top-bar-section ul').append('<li><a id="menuCheckoutItem" href="#checkout" class="openCheckout">Kasse<span class="amount">0,00 €</span></a></li>');
    reloadMenuCartItem();
});

$(document).on('click', '.closeModal', function(event) {
    event.preventDefault();

    $('a.close-reveal-modal').trigger('click');
});

$(document).on('click', '.addArticleToCart', function(event) {
    event.preventDefault();

    var form = $(this).closest('form');

    $('#articleAddedToBasketModal').foundation('reveal', 'open', {
        url: '?renderOnly=Cart.Modal.Item.Added',
        type: 'POST',
        data: {
            action: 'addArticleToCart',
            idart: $(this).data('idart'),
            qty: $('select[name="qty"]', form).val(),
            timestamp: new Date().getTime()
        },
        success: function() {
            reloadMenuCartItem();
        }
    });
});

$(document).on('click', '.openCartOverview', function(event) {
    event.preventDefault();

    $('#cartOverviewModal').foundation('reveal', 'open', {
        url: '?renderOnly=Cart.Modal.Overview',
        type: 'POST',
        data: {
            timestamp: new Date().getTime()
        }
    });
});

$(document).on('click', '.openCheckout', function(event) {
    event.preventDefault();

    $('#checkoutModal').foundation('reveal', 'open', {
        url: '?renderOnly=Cart.Modal.Checkout',
        type: 'POST',
        data: {
            timestamp: new Date().getTime()
        }
    });
});

$(document).on('click', '.openCheckoutDelivery', function(event) {
    event.preventDefault();

    $('#checkoutDeliveryModal').foundation('reveal', 'open');
});

$(document).on('click', '.openCheckoutBilling', function(event) {
    event.preventDefault();

    $('#checkoutBillingModal').foundation('reveal', 'open');
});

$(document).on('click', '.openCheckoutPayment', function(event) {
    event.preventDefault();

    $('#checkoutPaymentModal').foundation('reveal', 'open');
});

$(document).on('click', '.openCheckoutOverview', function(event) {
    event.preventDefault();

    $('#checkoutOverviewModal').foundation('reveal', 'open', {
        url: '?renderOnly=Cart.Modal.Checkout.Overview',
        type: 'POST',
        data: {
            timestamp: new Date().getTime()
        }
    });
});

$(document).on('click', '.openCheckoutDone', function(event) {
    event.preventDefault();

    $('#checkoutDoneModal').foundation('reveal', 'open', {
        url: '?renderOnly=Cart.Modal.Checkout.Done',
        type: 'POST',
        data: {
            timestamp: new Date().getTime()
        }
    });
});

$(document).on('change', '.modal_form', function() {
    $.post('#', {
        action: 'addFormData',
        data: $(this).serialize(),
        timestamp: new Date().getTime()
    });
});

$(document).on('click', '.doCheckout', function(event) {
    event.preventDefault();

    if (!$(this).hasClass('disabled')) {
        $.post('#', {
            action: 'doCheckout',
            timestamp: new Date().getTime()
        }, function(data) {
            if (data.success) {
                $('#doCheckout').submit();
            } else {
                alert('Da lief was schief :(');
            }
        });
    }
});

$(document).on('click', '#checkout_table_overview .delete', function(event) {
    event.preventDefault();

    var parentRow = $(this).parent('td').parent('tr');
    var article_id = $(parentRow).data('article-id');

    $.post('?ts=' + new Date().getTime(), {
        action: "deleteArticleFromCart",
        article_id: article_id
    }, function() {
        reloadMenuCartItem();

        $('#cartOverviewModal').load('?renderOnly=Cart.Modal.Overview', {
            timestamp: new Date().getTime()
        });
    });
});

$(document).on('change', '#checkout_required_checkbox_agb, #checkout_required_checkbox_datenschutz', function(event) {
    if ($('#checkout_required_checkbox_agb').is(':checked') && $('#checkout_required_checkbox_datenschutz').is(':checked')) {
        $('.doCheckout.button').removeClass('disabled');
    } else {
        $('.doCheckout.button').addClass('disabled');
    }
});

var reloadMenuCartItem = function() {
    $.post('?renderOnly=Cart.Menu.Info', {
        timestamp: new Date().getTime()
    }, function(data) {
        if (data.success) {
            var animationSpeed = 400;

            $('#menuCartOverviewItem .qty').fadeOut(animationSpeed, function() {
                $(this).html(data.qty).fadeIn(animationSpeed);
            });

            $('#menuCheckoutItem .amount').fadeOut(animationSpeed, function() {
                $(this).html(data.amount).fadeIn(animationSpeed);
            });
        }
    }, 'json');
};