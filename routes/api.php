<?php

route_post('login' , 'login');

if (session('user_data')) {
    route_post('user/charge/card' , 'charge_card');
    route_post('logout' ,'logout');
    route_get('user/used_bandwidth' , 'userUsedBandwidth');
    route_get('user/data' , 'getUserData');
    route_get('user/getchargeHistory' , 'getchargeHistory');
    route_get('user/getOffers' , 'getOffers');
    route_post('user/subscriptionRenewal' , 'subscriptionRenewal');
}

?>