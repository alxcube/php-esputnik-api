<?php

namespace Esputnik\Api;

use Esputnik\Exception\MissingArgumentException;
use Esputnik\Model\Contact as ContactModel;
use Esputnik\Model\Group as GroupModel;
use Esputnik\Model\MessageParam;
use Esputnik\Model\ParametrizedRecipient;

class Message extends AbstractApi
{
    /**
     * Отправка рассылки по заранее созданному сообщению. Сообщение может дополнительно параметризироваться.
     *
     * @param int             $id
     * @param ContactModel    $contact
     * @param boolean         $isEmail
     * @param MessageParam[]  $params
     * @param string          $fromName
     * @param array|null      $recipients
     * @param GroupModel|null $group
     * @return \Psr\Http\Message\StreamInterface
     */
    public function send(
        $id,
        ContactModel $contact,
        $isEmail,
        array $params,
        $fromName,
        array $recipients = null,
        GroupModel $group = null
    ) {
    
        $queryParams = [
            'contactId' => $contact->getId(),
            'email' => (bool) $isEmail,
            'params' => $params,
            'fromName' => $fromName,
        ];

        if ($group) {
            $queryParams['groupId'] = $group->getId();
        }

        if ($recipients) {
            $queryParams['recipients'] = $recipients;
        }

        return $this->post('message/'.rawurlencode($id).'/send', $queryParams);
    }

    /**
     * Отправка подготовленного сообщения одному или многим контактам.
     * Сообщение может параметризироваться для каждого контакта отдельно.
     *
     * @param int                     $id
     * @param ParametrizedRecipient[] $recipients
     * @param boolean                 $isEmail
     * @param string                  $fromName
     * @return string
     */
    public function smartSend($id, array $recipients, $isEmail, $fromName)
    {
        $queryParams = [
            'recipients' => $recipients,
            'email' => (bool) $isEmail,
            'fromName' => $fromName
        ];

        return $this->post('message/'.rawurlencode($id).'/smartsend', $queryParams);
    }

    /**
     * Отправить email-сообщение. Если контакта с таким адресом нет, он будет создан.
     *
     * @param $from
     * @param $subject
     * @param $htmlText
     * @param $plainText
     * @param array $emails
     * @param array $tags
     * @return \Psr\Http\Message\StreamInterface
     */
    public function email($from, $subject, $htmlText, $plainText, array $emails, array $tags)
    {
        $queryParams = [
            'from' => $from,
            'subject' => $subject,
            'htmlText' => $htmlText,
            'plainText' => $plainText,
            'emails' => $emails,
            'tags' => $tags,
        ];

        return $this->post('message/email/', $queryParams);
    }

    /**
     * Получить статус одиночного email сообщения.
     *
     * @param $ids
     * @return \Psr\Http\Message\StreamInterface
     */
    public function emailStatus($ids)
    {
        return $this->get('message/email/status/', [
            'ids' => join(',', $ids)
        ]);
    }

    /**
     * Отправить sms-сообщение. Если контакта с таким номером телефона нет, он будет создан.
     *
     * @param string $from
     * @param string $text
     * @param string[] $tags
     * @param string[] $phoneNumbers
     * @param int|null $groupId
     * @return \Psr\Http\Message\StreamInterface
     * @throws MissingArgumentException
     */
    public function sms($from, $text, array $tags = [], array $phoneNumbers = [], $groupId = null)
    {
        $queryParams = [
            'from' => $from,
            'text' => $text,
        ];
        if (!$phoneNumbers and !$groupId) {
            throw new MissingArgumentException(['phoneNumbers', 'groupId']);
        }

        if (!empty($phoneNumbers)) {
            $queryParams['phoneNumbers'] = $phoneNumbers;
        }
        if ($groupId) {
            $queryParams['groupId'] = $groupId;
        }
        if (!empty($tags)) {
            $queryParams['tags'] = $tags;
        }

        return $this->post('message/sms/', $queryParams);
    }

    /**
     * Получить статус одиночного sms сообщения.
     *
     * @param $ids
     * @return \Psr\Http\Message\StreamInterface
     */
    public function smsStatus($ids)
    {
        return $this->get('message/sms/status/', [
            'ids' => join(',', $ids)
        ]);
    }

    /**
     * Получить статус сообщения
     * @param $ids
     * @return \Psr\Http\Message\StreamInterface
     */
    public function status($ids)
    {
        return $this->get('message/status/', ['ids' => implode(',', $ids)]);
    }
}
