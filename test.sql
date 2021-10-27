CREATE PROCEDURE proc_getLocalChargeExcel (IN idcontract INT, IN port_o INT, IN port_d INT)
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
  ) as port_orig,
  (
    SELECT
      GROUP_CONCAT(DISTINCT(har.id) SEPARATOR ', ')
    FROM
      localcharports lcP
      INNER JOIN harbors har on har.id = lcP.port_orig
    WHERE
      lcP.localcharge_id = lc.id
  ) as port_orig_id,
  (
    SELECT
      GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ')
    FROM
      localcharports lcP
      INNER JOIN harbors har on har.id = lcP.port_dest
    WHERE
      lcP.localcharge_id = lc.id
  ) as port_dest,
  (
    SELECT
      GROUP_CONCAT(DISTINCT(har.id) SEPARATOR ', ')
    FROM
      localcharports lcP
      INNER JOIN harbors har on har.id = lcP.port_dest
    WHERE
      lcP.localcharge_id = lc.id
  ) as port_dest_id,
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
  INNER JOIN localcharports lcharP on lcharP.localcharge_id = lc.id
WHERE
  lc.contract_id = idcontract
  and td.id = 3
  and lcharP.port_orig = port_o
  and lcharP.port_dest = port_d