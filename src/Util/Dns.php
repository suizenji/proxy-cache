<?php

namespace App\Util;

use LibDNS\Messages\MessageFactory;
use LibDNS\Messages\MessageTypes;
use LibDNS\Records\QuestionFactory;
use LibDNS\Records\ResourceQTypes;
use LibDNS\Encoder\EncoderFactory;
use LibDNS\Decoder\DecoderFactory;

/** @see LibDNS\Examples AQuery.php */
class Dns {
    static function getA($queryName, $serverIP = '8.8.8.8')
    {
        $requestTimeout = 3;

        // Create question record
        $question = (new QuestionFactory)->create(ResourceQTypes::A);
        $question->setName($queryName);

        // Create request message
        $request = (new MessageFactory)->create(MessageTypes::QUERY);
        $request->getQuestionRecords()->add($question);
        $request->isRecursionDesired(true);

        // Encode request message
        $encoder = (new EncoderFactory)->create();
        $requestPacket = $encoder->encode($request);

        // Send request
        $socket = stream_socket_client("udp://$serverIP:53");
        stream_socket_sendto($socket, $requestPacket);
        $r = [$socket];
        $w = $e = [];
        if (!stream_select($r, $w, $e, $requestTimeout)) {
            echo "    Request timeout.\n";
            return false;
        }

        // Decode response message
        $decoder = (new DecoderFactory)->create();
        $responsePacket = fread($socket, 512);
        $response = $decoder->decode($responsePacket);

        // Handle response
        if ($response->getResponseCode() !== 0) {
            echo "    Server returned error code " . $response->getResponseCode() . ".\n";
            return false;
        }

        $answers = $response->getAnswerRecords();

        if (count($answers)) {
            foreach ($response->getAnswerRecords() as $record) {
                /** @var \LibDNS\Records\Resource $record */
#                echo "    " . $record->getData() . "\n";

                $data = (string) $record->getData();
                if (1 !== preg_match('/^([0-9.]+)$/', $data, $match)) {
                    continue;
                }

                return $match[1];
            }
        }

        return null;
    }
}
