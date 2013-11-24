<?php

work();

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

function generateFullEntry( $aContact )
{
    $aEntry =  array();
    $aEntry['Vorname [contact:firstname]']              = '';
    $aEntry['Name [contact:familyname]']                = $aContact['name'];
    $aEntry['Firma [contact:company]']                  = '';
    $aEntry['Straﬂe [address:street]']                  = '';
    $aEntry['PLZ [address:postcode]']                   = '';
    $aEntry['Stadt [address:city]']                     = '';
    $aEntry['Bundesland [address:state]']               = '';
    $aEntry['Rufnummer [telephone:phone]']              = $aContact['work'];
    $aEntry['Rufnummer [telephone:short dial(phone)]']  = '';
    $aEntry['Privat [telephone:homephone]']             = $aContact['home'];
    $aEntry['Privat [telephone:short dial(homephone)]'] = '';
    $aEntry['Mobil [telephone:mobile]']                 = $aContact['mobile'];
    $aEntry['Mobil [telephone:short dial(mobile)]']     = '';
    $aEntry['Fax [telephone:fax]']                      = '';
    $aEntry['Fax [telephone:short dial(fax)]']          = '';
    $aEntry['E-Mail [email:e-mail]']                    = '';
    $aEntry['URL [email:url]']                          = '';

    return $aEntry;

}

function exportToCsv( $aData )
{
    $fh = fopen( 'starface.csv', 'w' );
    
    $header = array(
                'Vorname [contact:firstname]',
                'Name [contact:familyname]',
                'Firma [contact:company]',
                'Straﬂe [address:street]',
                'PLZ [address:postcode]',
                'Stadt [address:city]',
                'Bundesland [address:state]',
                'Rufnummer [telephone:phone]',
                'Rufnummer [telephone:short dial(phone)]',
                'Privat [telephone:homephone]',
                'Privat [telephone:short dial(homephone)]',
                'Mobil [telephone:mobile]',
                'Mobil [telephone:short dial(mobile)]',
                'Fax [telephone:fax]',
                'Fax [telephone:short dial(fax)]',
                'E-Mail [email:e-mail]',
                'URL [email:url]'
        );
    
    fputcsv( $fh, $header, ';');
    
    foreach ( $aData as $row ) {
        fputcsv( $fh, $row, ';');
    }

    fclose($fh);
}

function work()
{
    $aSourceData = getFritzboxData();
    
    $aFullData = array();
    
    foreach( $aSourceData as $aContact)
    {
        $aFullData[] = generateFullEntry( $aContact );
    }
    
    exportToCsv( $aFullData );
    
    echo "Finished export!\n<br><a href='starface.csv'>Download the starface.csv</a>";
}
