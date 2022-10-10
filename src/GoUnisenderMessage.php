<?php

namespace NotificationChannels\GoUnisender;

use NotificationChannels\GoUnisender\Exceptions\GoUnisenderException;

class GoUnisenderMessage {
  /**
   * @var array
   */
  public $to = [];
  /**
   * @var array Объект, описывающий подстановки для конкретного получателя.
   */
  public $substitutions;
  /**
   * @var array Объект для передачи глобальных подстановок (например, название компании). Если названия переменных повторяются в объекте пользовательских подстановок “substitutions”, значения переменных будут взяты из объекта “substitutions”.
   */
  public $globalSubstitutions;
  /**
   * @var array Объект, который содержит в себе html, plaintext и amp части письма. Либо html, либо plaintext часть должна присутствовать обязательно.
   */
  public $body;
  /**
   * @var string Тема письма.
   */
  public $subject;
  /**
   * @var string Уникальный идентификатор шаблона. Если указан, то поля шаблона подставляются вместо пропущенных полей email/send. Например, если в email/send не указан body - берётся тело письма из шаблона, а если не указан subject - берётся тема из шаблона.
   */
  public $templateId;
  /**
   * @var string Заголовок для выбора языка ссылки и страницы отписки. Допустимые значения “be”, “de”, “en”, “es”, “fr”, “it”, “pl”, “pt”, “ru”, “ua”.
   */
  public $globalLanguage = 'ru';
  /**
   * @var int|null Пропустить или не пропускать добавление стандартного блока со ссылкой отписки к HTML-части письма.
   */
  public $skipUnsubscribe;

  /**
   * Установить email адрес и подстановки получателей.
   * @param string|array $to
   * @return \NotificationChannels\GoUnisender\GoUnisenderMessage
   */
  public function setTo($to): GoUnisenderMessage {
    if (is_string($to)) {
      $to = ['email' => $to];
    }

    if (!empty($to['email'])) {
      $to = [['email' => $to['email'], 'substitutions' => $to['substitutions'] ?? []]];
    }

    $this->to = [];
    foreach ($to as $toItem) {
      $this->addTo($toItem);
    }

    return $this;
  }

  /**
   * Добавить получателя
   * Examples:
   *  addTo(['email' => 'example@info.ru', 'substitutions' => ['first_name' => 'Ivan']])
   *  addTo('example@info.ru', ['first_name' => 'Ivan'])
   * @param array|string $email
   * @param array $substitutions
   * @param bool $overwrite
   * @return \NotificationChannels\GoUnisender\GoUnisenderMessage
   */
  public function addTo($email, array $substitutions = [], bool $overwrite = FALSE): GoUnisenderMessage {
    if ($overwrite) {
      $this->to = [];
    }

    if (is_array($email)) {
      $to = $email;
    } elseif (is_string($email)) {
      $to = ['email' => $email, 'substitutions' => $substitutions];
    }

    if (empty($to['email'])) {
      throw new GoUnisenderException('No receivers.');
    }

    $this->to[] = $to;

    return $this;
  }

  /**
   * Установить тело письма
   * @param array|string $body
   * @param string $type
   * @return $this
   */
  public function setBody($body, string $type = 'html'): GoUnisenderMessage {
    if (is_string($body)) {
      return $this->setBody([$type => $body]);
    }

    if (is_array($body)) {
      if (!array_key_exists('html', $body) && !array_key_exists('plaintext', $body)) {
        throw new GoUnisenderException('Неизвестный тип письма');
      }

      if (array_key_exists('html', $body)) {
        $this->body['html'] = $body['html'];
      }
      if (array_key_exists('plaintext', $body)) {
        $this->body['plaintext'] = $body['plaintext'];
      }

      return $this;
    }

    throw new GoUnisenderException('Неверный тип body');
  }

  /**
   * Установка глобальных подстановок
   * @param array $substitutions
   * @param bool $global
   * @return $this
   */
  public function setSubstitutions(array $substitutions, bool $global = FALSE): GoUnisenderMessage {
    if ($global) {
      $this->globalSubstitutions = $substitutions;
    } else {
      $this->substitutions = $substitutions;
    }

    return $this;
  }

  /**
   * Установка идентификатора шаблона
   * @param string $templateId
   * @return $this
   */
  public function setTemplateId(string $templateId): GoUnisenderMessage {
    $this->templateId = $templateId;
    return $this;
  }

  public function setGlobalLanguage(string $lang): GoUnisenderMessage {
    if (!in_array($lang, ['be', 'de', 'en', 'es', 'fr', 'it', 'pl', 'pt', 'ru', 'ua'])) {
      throw new GoUnisenderException('Указан недопустимый язык');
    }

    $this->globalLanguage = $lang;

    return $this;
  }

  public function setSkipUnsubscribe(bool $skip): GoUnisenderMessage {
    $this->skipUnsubscribe = $skip === TRUE ? 1 : 0;

    return $this;
  }
}
