<?php

namespace App\Http\Controllers\Clients;

interface ChatClientContract
{
  public function chat(array $messages): string;
}
