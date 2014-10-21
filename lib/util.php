<?php

/*
Copyright (c) 2013-2014, Microsoft Mobile
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name of the {organization} nor the names of its
  contributors may be used to endorse or promote products derived from
  this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

function getTimezoneByCountry($countryId)
{
    $timezones = array();
    $timezones["us"] = "America/New_York";
    $timezones["us"] = "America/Chicago";
    $timezones["us"] = "America/Denver";
    $timezones["us"] = "America/Los_Angeles";
    $timezones["us"] = "America/Anchorage";
    $timezones["us"] = "America/Halifax";

    $timezones["ca"] = "Canada/Pacific";
    $timezones["ca"] = "Canada/Mountain";
    $timezones["ca"] = "Canada/Central";
    $timezones["ca"] = "Canada/Eastern";
    $timezones["ca"] = "Canada/Atlantic";

    $timezones["au"] = "Australia/Sydney";
    $timezones["au"] = "Australia/Darwin";
    $timezones["au"] = "Australia/Perth";

    $timezones["ru"] = "Europe/Moscow";
    $timezones["ru"] = "Europe/Samara";
    $timezones["ru"] = "Asia/Yekaterinburg";
    $timezones["ru"] = "Asia/Novosibirsk";
    $timezones["ru"] = "Asia/Krasnoyarsk";
    $timezones["ru"] = "Asia/Irkutsk";
    $timezones["ru"] = "Asia/Chita";
    $timezones["ru"] = "Asia/Vladivostok";

    $timezones["an"] = "Europe/Andorra";
    $timezones["ae"] = "Asia/Abu_Dhabi";
    $timezones["af"] = "Asia/Kabul";
    $timezones["al"] = "Europe/Tirana";
    $timezones["am"] = "Asia/Yerevan";
    $timezones["ao"] = "Africa/Luanda";
    $timezones["ar"] = "America/Buenos_Aires";
    $timezones["as"] = "Pacific/Samoa";
    $timezones["at"] = "Europe/Vienna";
    $timezones["aw"] = "America/Aruba";
    $timezones["az"] = "Asia/Baku";

    $timezones["ba"] = "Europe/Sarajevo";
    $timezones["bb"] = "America/Barbados";
    $timezones["bd"] = "Asia/Dhaka";
    $timezones["be"] = "Europe/Brussels";
    $timezones["bf"] = "Africa/Ouagadougou";
    $timezones["bg"] = "Europe/Sofia";
    $timezones["bh"] = "Asia/Bahrain";
    $timezones["bi"] = "Africa/Bujumbura";
    $timezones["bm"] = "Atlantic/Bermuda";
    $timezones["bn"] = "Asia/Brunei";
    $timezones["bo"] = "America/La_Paz";
    $timezones["br"] = "America/Sao_Paulo";
    $timezones["bs"] = "America/Nassau";
    $timezones["bw"] = "Gaborone";
    $timezones["by"] = "Europe/Minsk";
    $timezones["bz"] = "America/Belize";

    $timezones["cd"] = "Africa/Kinshasa";
    $timezones["ch"] = "Europe/Zurich";
    $timezones["ci"] = "Africa/Abidjan";
    $timezones["cl"] = "America/Santiago";
    $timezones["cn"] = "Asia/Shanghai";
    $timezones["co"] = "America/Bogota";
    $timezones["cr"] = "America/Costa_Rica";
    $timezones["cu"] = "America/Cuba";
    $timezones["cv"] = "Atlantic/Cape_Verde";
    $timezones["cy"] = "Asia/Nicosia";
    $timezones["cz"] = "Europe/Prague";

    $timezones["de"] = "Europe/Berlin";
    $timezones["dj"] = "Africa/Djibouti";
    $timezones["dk"] = "Europe/Copenhagen";
    $timezones["do"] = "America/Santo_Domingo";
    $timezones["dz"] = "Africa/Algiers";

    $timezones["ec"] = "America/Quito";
    $timezones["ee"] = "Europe/Tallinn";
    $timezones["eg"] = "Africa/Cairo";
    $timezones["er"] = "Africa/Asmara";
    $timezones["es"] = "Europe/Madrid";

    $timezones["fi"] = "Europe/Helsinki";
    $timezones["fj"] = "Pacific/Fiji";
    $timezones["fk"] = "America/Stanley";
    $timezones["fr"] = "Europe/Paris";

    $timezones["ga"] = "Africa/Libreville";
    $timezones["gb"] = "Europe/London";
    $timezones["gd"] = "America/Grenada";
    $timezones["ge"] = "Asia/Tbilisi";
    $timezones["gh"] = "Africa/Accra";
    $timezones["gm"] = "Africa/Banjul";
    $timezones["gn"] = "Africa/Conakry";
    $timezones["gr"] = "Europe/Athens";
    $timezones["gy"] = "America/Guyana";

    $timezones["hk"] = "Asia/Hong_Kong";
    $timezones["hn"] = "America/Tegucigalpa";
    $timezones["hr"] = "Europe/Zagreb";
    $timezones["ht"] = "America/Port-au-Prince";
    $timezones["hu"] = "Europe/Budapest";

    $timezones["id"] = "Asia/Jakarta";
    $timezones["ie"] = "Europe/Dublin";
    $timezones["il"] = "Asia/Tel_Aviv";
    $timezones["in"] = "Asia/Calcutta";
    $timezones["iq"] = "Asia/Baghdad";
    $timezones["ir"] = "Asia/Tehran";
    $timezones["is"] = "Atlantic/Reykjavik";
    $timezones["it"] = "Europe/Rome";

    $timezones["jm"] = "America/Jamaica";
    $timezones["jo"] = "Asia/Amman";
    $timezones["jp"] = "Asia/Tokyo";

    $timezones["ke"] = "Africa/Nairobi";
    $timezones["kg"] = "Asia/Bishkek";
    $timezones["kh"] = "Asia/Phnom_Penh";
    $timezones["kp"] = "Asia/Pyongyang";
    $timezones["kr"] = "Asia/Seoul";
    $timezones["kw"] = "Asia/Kuwait";

    $timezones["lb"] = "Asia/Beirut";
    $timezones["li"] = "Europe/Liechtenstein";
    $timezones["lk"] = "Asia/Colombo";
    $timezones["lr"] = "Africa/Monrovia";
    $timezones["ls"] = "Africa/Maseru";
    $timezones["lt"] = "Europe/Vilnius";
    $timezones["lu"] = "Europe/Luxembourg";
    $timezones["lv"] = "Europe/Riga";
    $timezones["ly"] = "Africa/Tripoli";

    $timezones["ma"] = "Africa/Rabat";
    $timezones["mc"] = "Europe/Monaco";
    $timezones["md"] = "Europe/Chisinau";
    $timezones["mg"] = "Indian/Antananarivo";
    $timezones["mk"] = "Europe/Skopje";
    $timezones["ml"] = "Africa/Bamako";
    $timezones["mm"] = "Asia/Rangoon";
    $timezones["mn"] = "Asia/Ulaanbaatar";
    $timezones["mo"] = "Asia/Macao";
    $timezones["mq"] = "America/Martinique";
    $timezones["mt"] = "Europe/Malta";
    $timezones["mu"] = "Indian/Mauritius";
    $timezones["mv"] = "Indian/Maldives";
    $timezones["mw"] = "Africa/Lilongwe";
    $timezones["mx"] = "America/Mexico_City";
    $timezones["my"] = "Asia/Kuala_Lumpur";

    $timezones["na"] = "Africa/Windhoek";
    $timezones["ne"] = "Africa/Niamey";
    $timezones["ng"] = "Africa/Lagos";
    $timezones["ni"] = "America/Managua";
    $timezones["nl"] = "Europe/Amsterdam";
    $timezones["no"] = "Europe/Oslo";
    $timezones["np"] = "Asia/Kathmandu";
    $timezones["nz"] = "Pacific/Aukland";

    $timezones["om"] = "Asia/Muscat";

    $timezones["pa"] = "America/Panama";
    $timezones["pe"] = "America/Lima";
    $timezones["pg"] = "Pacific/Port_Moresby";
    $timezones["ph"] = "Asia/Manila";
    $timezones["pk"] = "Asia/Karachi";
    $timezones["pl"] = "Europe/Warsaw";
    $timezones["pr"] = "America/Puerto_Rico";
    $timezones["pt"] = "Europe/Lisbon";
    $timezones["py"] = "America/Asuncion";

    $timezones["qa"] = "Asia/Qatar";

    $timezones["ro"] = "Europe/Bucharest";
    $timezones["rs"] = "Europe/Belgrade";

    $timezones["rw"] = "Africa/Kigali";

    $timezones["sa"] = "Asia/Riyadh";
    $timezones["sd"] = "Africa/Khartoum";
    $timezones["se"] = "Europe/Stockholm";
    $timezones["sg"] = "Asia/Singapore";
    $timezones["si"] = "Europe/Ljubljana";
    $timezones["sk"] = "Europe/Bratislava";
    $timezones["sl"] = "Africa/Freetown";
    $timezones["so"] = "Africa/Mogadishu";
    $timezones["sr"] = "America/Paramaribo";
    $timezones["sv"] = "America/El_Salvador";
    $timezones["sy"] = "Asia/Damascus";
    $timezones["sz"] = "Africa/Mbabane";

    $timezones["td"] = "Africa/Ndjamena";
    $timezones["tg"] = "Africa/Lome";
    $timezones["th"] = "Asia/Bangkok";
    $timezones["tj"] = "Asia/Dushanbe";
    $timezones["tm"] = "Asia/Ashgabat";
    $timezones["tn"] = "Africa/Tunis";
    $timezones["to"] = "Pacific/Tongatapu";
    $timezones["tr"] = "Asia/Istanbul";
    $timezones["tw"] = "Asia/Taipei";
    $timezones["tz"] = "Africa/Dar_es_Salaam";

    $timezones["ua"] = "Europe/Kiev";
    $timezones["ug"] = "Africa/Kampala";
    $timezones["uk"] = "Europe/London";
    $timezones["uy"] = "America/Montevideo";
    $timezones["uz"] = "Asia/Tashkent";

    $timezones["ve"] = "America/Caracas";
    $timezones["vn"] = "Asia/Hanoi";

    $timezones["za"] = "Africa/Johannesburg";
    $timezones["zm"] = "Africa/Lusaka";
    $timezones["zw"] = "Africa/Harare";

    $countryId = strtolower($countryId);
    if (array_key_exists($countryId, $timezones)) {
        return $timezones[$countryId];
    }

    return "Europe/Helsinki";
}

?>
