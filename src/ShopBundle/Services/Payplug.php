<?php

namespace AppBundle\Services;

class Crypt {

    public function createPayment($commande) {
        $payment = \Payplug\Payment::create(array(
            'amount'            => $comma,
            'currency'          => 'EUR',
            'customer'          => array(
                'email'             => 'john.doe@example.com',
                'first_name'        => 'John',
                'last_name'         => 'Doe'
            ),
            'hosted_payment'    => array(
                'return_url'        => 'https://www.example.com/thank_you_for_your_payment.html',
                'cancel_url'        => 'https://www.example.com/so_bad_it_didnt_make_it.html'
            ),
            'notification_url'      => 'http://www.example.com/callbackURL'
        ));
    }
} 