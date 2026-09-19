<?php

use App\Models\User;

test('la homepage è accessibile a tutti', function () {
    $response = $this->get(route('home'));
    $response->assertOk();
});

test('la homepage contiene il titolo principale', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Viga Special Week');
});

test('la homepage contiene il sottotitolo', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('La tua settimana per approfondire, crescere e recuperare.');
});

test('guest users vedono il link login', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Entra');
});

test('guest users vedono il link attività disponibili', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Attività disponibili');
});

test('utenti autenticati vedono il link dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));
    $response->assertSeeText('Dashboard');
});

test('la homepage contiene la sezione Come funziona', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Come funziona');
    $response->assertSeeText('Sfoglia');
    $response->assertSeeText('Accedi');
    $response->assertSeeText('Scegli');
    $response->assertSeeText('Partecipa');
});

test('la homepage contiene la sezione I numeri della Viga Special Week', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('I numeri della Viga Special Week');
    $response->assertSeeText('Attività disponibili');
    $response->assertSeeText('Insegnanti coinvolti');
    $response->assertSeeText('Ore di arricchimento');
});

test('la homepage contiene la sezione FAQ', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Domande frequenti');
    $response->assertSeeText('Che cos\'è la Viga Special Week?');
});

test('la homepage contiene i link di navigazione anchor', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('scopri le attività');
    $response->assertSeeText('come funziona');
    $response->assertSeeText('i numeri');
    $response->assertSeeText('dubbi e domande frequenti');
});

test('la homepage contiene le feature della sezione attività', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Attività varie');
    $response->assertSeeText('Libertà di scelta');
    $response->assertSeeText('Conosci altri studenti');
});

test('la homepage contiene le informazioni sulla settimana', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Scopri le attività di potenziamento disponibili');
    $response->assertSeeText('Puoi iniziare a sfogliare tutte le attività proposte anche senza accedere.');
});

test('banner manutenzione non appare per guest users', function () {
    $response = $this->get(route('home'));
    $response->assertDontSee('x-maintenance-banner', escape: false);
});

test('banner manutenzione non appare per utenti normali', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));
    $response->assertDontSee('x-maintenance-banner', escape: false);
});

test('banner impersonazione non appare per utenti normali', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));
    $response->assertDontSee('x-impersonation-banner', escape: false);
});

test('la homepage contiene il footer con il credito', function () {
    $response = $this->get(route('home'));
    $response->assertSeeText('Made with');
    $response->assertSeeText('Viga Special Week team');
});

test('i link della navigazione anchor utilizzano scroll-mt-20', function () {
    $response = $this->get(route('home'));
    $response->assertSee('id="attivita-disponibili"', escape: false);
    $response->assertSee('id="come-funziona"', escape: false);
    $response->assertSee('id="numeri"', escape: false);
    $response->assertSee('id="faq"', escape: false);
});
