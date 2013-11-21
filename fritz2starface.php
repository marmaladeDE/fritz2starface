<?php

function getFritzboxData()
{
    $path = __DIR__.'/fritzbox.xml';
    
    $xml = new SimpleXMLElement( $path, 0, true );
    
    $i = 0;
    
    foreach($xml->phonebook->contact as $contact)
    {
        $aData[$i]['name']          = (string) $contact->person->realName;
        $aData[$i]                  = array_merge($aData[$i], selectNumbers($contact->telephony));
        $i++;
    }
    
    return $aData;
    
}

function selectNumbers( $phonenode )
{
    $aNumbers = array();
    
    foreach($phonenode->number as $number)
    {
        switch((string)$number['type'])
        {
            case 'work':
                $aNumbers['work']   = (string) $number;
                break;
            case 'home':
                $aNumbers['home']   = (string) $number;
                break;
            case 'mobile':
                $aNumbers['mobile'] = (string) $number;
                break;
        }
    }
    return $aNumbers;
}