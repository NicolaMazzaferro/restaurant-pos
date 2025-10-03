<?php

it('returns 401 on products index when unauthenticated', function () {
    $this->getJson('/api/products')->assertStatus(401);
});