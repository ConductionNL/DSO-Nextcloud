export type TKlantType = 'persoon' | 'organisatie'

export type TKlant = {
    id: string;
    type: TKlantType;
    voornaam: string;
    tweedeVoornaam: string;
    tussenvoegsel: string;
    achternaam: string;
    bsn: string;
    geboortedatum: string;
    geslacht: 'man' | 'vrouw' | 'overige';
    land: string;
    telefoonnummer: string;
    emailadres: string;
    straatnaam: string;
    plaats: string;
    postcode: string;
    huisnummer: string;
    functie: string;
    aanmaakkanaal: string;
    bronorganisatie: string;
    bedrijfsnaam: string;
    kvkNummer: string;
    websiteUrl: string;
    url: string;
    geverifieerd: string;
    subject: string;
    subjectIdentificatie: string;
    subjectType: string;
}
