<?php

namespace App\Helpers;


class CustomValidator
{
  private array $input;
  private array $rules;
  private array $messages;
  private string $formName;

  public function __construct(array $input)
  {
    $this->input = $input;
  }

  public function getRules()
  {
    return $this->rules;
  }

  public function setFormName($formName)
  {
    $this->formName = $formName;
  }

  public function getMessages()
  {
    return $this->messages;
  }

  private function rulePrefix()
  {
    return $this->formName ? $this->formName . '.' : '';
  }

  private function messagePrefix(string $key)
  {

    if (isset($this->formName) && $this->formName !== '') {

      return  $this->formName . '.' . $key;
    }
    return $key;
  }

  public function generateLinkRules()
  {
    foreach ($this->input as $key => $val) {

      if (str_contains($key, 'url-')) {

        $this->rules[$this->rulePrefix() . $key] = ['nullable', 'regex:/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/', 'max:70'];
      }
    }
    $this->rules = isset($this->rules) ? $this->rules : [];
  }

  public function generateLinkMessages()
  {

    foreach ($this->input as $key => $val) {

      if (str_contains($key, 'url-')) {

        $this->messages[$this->messagePrefix($key) . '.' . 'max'] = 'Maximum allowed characters is 50';
        $this->messages[$this->messagePrefix($key) . '.' . 'regex'] = 'The URL format is invalid';
      }
    }
    $this->messages = isset($this->messages) ? $this->messages : [];
  }
}
