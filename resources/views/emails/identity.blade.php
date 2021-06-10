@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => 'https://www.carnet.hr'])
            <img src="https://www.carnet.hr/wp-content/themes/carnet/assets/img/logo.svg"/>
        @endcomponent
    @endslot

    Poštovani/a {{$user['first_name']}} {{$user['last_name']}},

    <br/>

    dostavljamo korisničke podatake o vašem <b> elektroničkom identitetu </b>.

    <b> Elektronički identitet </b> je skup podataka kojim se utvrđuje identitet osobe.

    Osnovni podaci o Vašem elektroničkom identitetu su:

    <br/>
    <h2 style="text-align: center">Korisnička oznaka: <b> {{$credentials['uid']}} </b> </h2>
    <h2 style="text-align: center">Zaporka: <b> {{$credentials['password']}} </b></h2>
    <br/>

    Korisničke podatke, korisničku oznaku i zaporku, treba unositi doslovno onako kako su napisani i pri tome paziti na velika i mala slova.
    {{--Svoj elektronički identitet možete koristiti i za sljedeće usluge:--}}

    {{--- besplatni pristup internetu putem usluge eduroam
    @component('mail::button', ['url' => 'https://eduroam.sum.ba'])
        Eduroam
    @endcomponent--}}

    <br/>
    <h6 style="text-align: center">CARNET</h6>
    <br/>

    @slot('footer')
        @component('mail::footer')
            <b>Mail:</b> helpdesk@carnet.hr<br>
            <b>Web:</b> <u>https://www.carnet.hr</u><br>
            <b>Adresa:</b> Josipa Marohnića 5, 10000 Zagreb, Hrvatska
            {{--<img src="https://aai.sum.ba/korisnik/images/logo.png"/>--}}
        @endcomponent
    @endslot

@endcomponent
