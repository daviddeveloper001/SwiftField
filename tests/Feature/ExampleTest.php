<?php

test('the application returns a successful response', function () {
    $response = $this->get('/registrar-mi-negocio');

    $response->assertStatus(200);
});
