<?php

it('renders the default x-ray view', function () {
    $this->get('/x-ray')
        ->assertOk()
        ->assertSee('x-ray UI assets are installed');
});
