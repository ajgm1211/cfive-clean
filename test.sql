CREATE PROCEDURE `cargofive`.`proc_getLocalChargeExcel4`(
    IN idcontract INT,
    IN port_o varchar(100),
    IN port_d varchar(100),
    IN country_o varchar(100),
    IN country_d varchar(100)
)
select
    lc.id,
    lc.contract_id,
    sr.name as surcharge,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ')
        FROM
            localcharports lcP
            INNER JOIN harbors har on har.id = lcP.port_orig
        WHERE
            lcP.localcharge_id = lc.id
            and FIND_IN_SET(har.id, port_o)
    ) as port_orig,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(har.id) SEPARATOR ', ')
        FROM
            localcharports lcP
            INNER JOIN harbors har on har.id = lcP.port_orig
        WHERE
            lcP.localcharge_id = lc.id
            and FIND_IN_SET(har.id, port_o)
    ) as port_orig_id,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ')
        FROM
            localcharports lcP
            INNER JOIN harbors har on har.id = lcP.port_dest
        WHERE
            lcP.localcharge_id = lc.id
            and FIND_IN_SET(har.id, port_d)
    ) as port_dest,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(har.id) SEPARATOR ', ')
        FROM
            localcharports lcP
            INNER JOIN harbors har on har.id = lcP.port_dest
        WHERE
            lcP.localcharge_id = lc.id
            and FIND_IN_SET(har.id, port_d)
    ) as port_dest_id,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(coun.name) SEPARATOR ', ')
        FROM
            localcharcountry lcCO
            INNER JOIN countries coun on coun.id = lcCO.country_orig
        WHERE
            lcCO.localcharge_id = lc.id
            and FIND_IN_SET(coun.id, country_o)
    ) as country_orig,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(coun.id) SEPARATOR ', ')
        FROM
            localcharcountry lcCO
            INNER JOIN countries coun on coun.id = lcCO.country_orig
        WHERE
            lcCO.localcharge_id = lc.id
            and FIND_IN_SET(coun.id, country_o)
    ) as country_orig_id,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(counD.name) SEPARATOR ', ')
        FROM
            localcharcountry lcCD
            INNER JOIN countries counD on counD.id = lcCD.country_dest
        WHERE
            lcCD.localcharge_id = lc.id
            and FIND_IN_SET(counD.id, country_d)
    ) as country_dest,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(counD.id) SEPARATOR ', ')
        FROM
            localcharcountry lcCD
            INNER JOIN countries counD on counD.id = lcCD.country_dest
        WHERE
            lcCD.localcharge_id = lc.id
            and FIND_IN_SET(counD.id, country_d)
    ) as country_dest_id,
    td.description changetype,
    (
        SELECT
            GROUP_CONCAT(DISTINCT(carr.name) SEPARATOR ', ')
        FROM
            localcharcarriers lcC
            INNER JOIN carriers carr on carr.id = lcC.carrier_id
        WHERE
            lcC.localcharge_id = lc.id
    ) as carrier,
    ctype.name calculation_type,
    ctype.id calculation_type_id,
    cur.alphacode as currency,
    cur.id as currency_id,
    lc.ammount
from
    localcharges lc
    INNER JOIN surcharges sr on sr.id = lc.surcharge_id
    INNER JOIN typedestiny td on td.id = lc.typedestiny_id
    INNER JOIN currency cur on cur.id = lc.currency_id
    INNER JOIN calculationtype ctype on ctype.id = lc.calculationtype_id
    LEFT JOIN localcharports lcharP on lcharP.localcharge_id = lc.id
    LEFT JOIN localcharcountry lcharC on lcharC.localcharge_id = lc.id
WHERE
    lc.contract_id = idcontract
    and td.id = 3
    and (
        (
            FIND_IN_SET(lcharP.port_orig, port_o)
            and FIND_IN_SET(lcharP.port_dest, port_d)
        )
        OR (
            FIND_IN_SET(lcharC.country_orig, country_o)
            and FIND_IN_SET(lcharC.country_dest, country_d)
        )
    );