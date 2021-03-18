-- ebdb.view_quote_v2s source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `view_quote_v2s` AS (
select
    `quote_v2s`.`id` AS `id`,
    `quote_v2s`.`company_user_id` AS `company_user_id`,
    `quote_v2s`.`user_id` AS `user_id`,
    `quote_v2s`.`quote_id` AS `quote_id`,
    `quote_v2s`.`custom_quote_id` AS `custom_quote_id`,
    `companies`.`business_name` AS `business_name`,
    `companies`.`id` AS `company_id`,
    `quote_v2s`.`created_at` AS `created_at`,
    concat(`users`.`name`, ' ', `users`.`lastname`) AS `owner`,
    (
    select
        group_concat(distinct `harbors`.`display_name` separator '| ')
    from
        (`automatic_rates`
    join `harbors` on
        ((`harbors`.`id` = `automatic_rates`.`origin_port_id`)))
    where
        (`quote_v2s`.`id` = `automatic_rates`.`quote_id`)) AS `origin_port`,
    (
    select
        group_concat(distinct `harbors`.`display_name` separator '| ')
    from
        (`automatic_rates`
    join `harbors` on
        ((`harbors`.`id` = `automatic_rates`.`destination_port_id`)))
    where
        (`quote_v2s`.`id` = `automatic_rates`.`quote_id`)) AS `destination_port`,
    (
    select
        group_concat(distinct `airports`.`display_name` separator '| ')
    from
        (`automatic_rates`
    join `airports` on
        ((`airports`.`id` = `automatic_rates`.`origin_airport_id`)))
    where
        (`quote_v2s`.`id` = `automatic_rates`.`quote_id`)) AS `origin_airport`,
    (
    select
        group_concat(distinct `airports`.`display_name` separator '| ')
    from
        (`automatic_rates`
    join `airports` on
        ((`airports`.`id` = `automatic_rates`.`destination_airport_id`)))
    where
        (`quote_v2s`.`id` = `automatic_rates`.`quote_id`)) AS `destination_airport`,
    `quote_v2s`.`type` AS `type`
from
    ((`quote_v2s`
left join `companies` on
    ((`quote_v2s`.`company_id` = `companies`.`id`)))
join `users` on
    ((`quote_v2s`.`user_id` = `users`.`id`)))
where
    isnull(`quote_v2s`.`deleted_at`));


-- ebdb.views_contract_rates source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_contract_rates` AS (
select
    `ra`.`id` AS `id`,
    `cont`.`company_user_id` AS `company_user_id`,
    `cont`.`id` AS `contract_id`,
    `cont`.`name` AS `name`,
    `cont`.`number` AS `number`,
    `cont`.`validity` AS `validy`,
    `cont`.`expire` AS `expire`,
    `cont`.`status` AS `status`,
    `har_orig`.`display_name` AS `port_orig`,
    `har_dest`.`display_name` AS `port_dest`,
    `car`.`name` AS `carrier`,
    `ra`.`twuenty` AS `twuenty`,
    `ra`.`forty` AS `forty`,
    `ra`.`fortyhc` AS `fortyhc`,
    `ra`.`fortynor` AS `fortynor`,
    `ra`.`fortyfive` AS `fortyfive`,
    `curr`.`alphacode` AS `currency`,
    `sh`.`name` AS `schedule_type`,
    `ra`.`transit_time` AS `transit_time`,
    `ra`.`via` AS `via`
from
    ((((((`rates` `ra`
join `harbors` `har_orig` on
    ((`har_orig`.`id` = `ra`.`origin_port`)))
join `harbors` `har_dest` on
    ((`har_dest`.`id` = `ra`.`destiny_port`)))
join `carriers` `car` on
    ((`car`.`id` = `ra`.`carrier_id`)))
join `currency` `curr` on
    ((`curr`.`id` = `ra`.`currency_id`)))
left join `schedule_type` `sh` on
    ((`sh`.`id` = `ra`.`schedule_type_id`)))
join `contracts` `cont` on
    ((`cont`.`id` = `ra`.`contract_id`))));


-- ebdb.views_contractlcl_rates source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_contractlcl_rates` AS (
select
    `ra`.`id` AS `id`,
    `cont`.`company_user_id` AS `company_user_id`,
    `cont`.`id` AS `contract_id`,
    `cont`.`name` AS `name`,
    `cont`.`number` AS `number`,
    `cont`.`validity` AS `validy`,
    `cont`.`expire` AS `expire`,
    `cont`.`status` AS `status`,
    `har_orig`.`display_name` AS `port_orig`,
    `har_dest`.`display_name` AS `port_dest`,
    `car`.`name` AS `carrier`,
    `ra`.`uom` AS `uom`,
    `ra`.`minimum` AS `minimum`,
    `curr`.`alphacode` AS `currency`,
    `sh`.`name` AS `schedule_type`,
    `ra`.`transit_time` AS `transit_time`,
    `ra`.`via` AS `via`
from
    ((((((`rates_lcl` `ra`
join `harbors` `har_orig` on
    ((`har_orig`.`id` = `ra`.`origin_port`)))
join `harbors` `har_dest` on
    ((`har_dest`.`id` = `ra`.`destiny_port`)))
join `carriers` `car` on
    ((`car`.`id` = `ra`.`carrier_id`)))
join `currency` `curr` on
    ((`curr`.`id` = `ra`.`currency_id`)))
left join `schedule_type` `sh` on
    ((`sh`.`id` = `ra`.`schedule_type_id`)))
join `contracts_lcl` `cont` on
    ((`cont`.`id` = `ra`.`contractlcl_id`))));


-- ebdb.views_globalcharges source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_globalcharges` AS (
select
    `gc`.`id` AS `id`,
    `sr`.`name` AS `charge`,
    `td`.`description` AS `charge_type`,
    `ctype`.`name` AS `calculation_type`,
    (
    select
        group_concat(distinct `har`.`code` separator ', ')
    from
        (`globalcharport` `gcP`
    join `harbors` `har` on
        ((`har`.`id` = `gcP`.`port_orig`)))
    where
        (`gcP`.`globalcharge_id` = `gc`.`id`)) AS `origin_port`,
    (
    select
        group_concat(distinct `har`.`code` separator ', ')
    from
        (`globalcharport` `gcP`
    join `harbors` `har` on
        ((`har`.`id` = `gcP`.`port_dest`)))
    where
        (`gcP`.`globalcharge_id` = `gc`.`id`)) AS `destination_port`,
    (
    select
        group_concat(distinct `coun`.`name` separator ', ')
    from
        (`globalcharcountry` `gcCO`
    join `countries` `coun` on
        ((`coun`.`id` = `gcCO`.`country_orig`)))
    where
        (`gcCO`.`globalcharge_id` = `gc`.`id`)) AS `origin_country`,
    (
    select
        group_concat(distinct `counD`.`name` separator ', ')
    from
        (`globalcharcountry` `gcCD`
    join `countries` `counD` on
        ((`counD`.`id` = `gcCD`.`country_dest`)))
    where
        (`gcCD`.`globalcharge_id` = `gc`.`id`)) AS `destination_country`,
    (
    select
        group_concat(distinct `carr`.`name` separator ', ')
    from
        (`globalcharcarrier` `gcC`
    join `carriers` `carr` on
        ((`carr`.`id` = `gcC`.`carrier_id`)))
    where
        (`gcC`.`globalcharge_id` = `gc`.`id`)) AS `carrier`,
    (
    select
        group_concat(distinct `carr`.`uncode` separator ', ')
    from
        (`globalcharcarrier` `gcC`
    join `carriers` `carr` on
        ((`carr`.`id` = `gcC`.`carrier_id`)))
    where
        (`gcC`.`globalcharge_id` = `gc`.`id`)) AS `carriers`,
    `gc`.`ammount` AS `amount`,
    `cur`.`alphacode` AS `currency_code`,
    `gc`.`validity` AS `valid_from`,
    `gc`.`expire` AS `valid_until`,
    `gc`.`company_user_id` AS `company_user_id`,
    `cmpu`.`name` AS `company_user`
from
    (((((`globalcharges` `gc`
join `surcharges` `sr` on
    ((`sr`.`id` = `gc`.`surcharge_id`)))
join `typedestiny` `td` on
    ((`td`.`id` = `gc`.`typedestiny_id`)))
join `currency` `cur` on
    ((`cur`.`id` = `gc`.`currency_id`)))
join `calculationtype` `ctype` on
    ((`ctype`.`id` = `gc`.`calculationtype_id`)))
join `company_users` `cmpu` on
    ((`cmpu`.`id` = `gc`.`company_user_id`))));


-- ebdb.views_globalcharges_lcl source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_globalcharges_lcl` AS (
select
    `gbl`.`id` AS `id`,
    (
    select
        group_concat(distinct `har`.`display_name` separator ' | ')
    from
        (`globalcharports_lcl` `gbp`
    join `harbors` `har` on
        ((`har`.`id` = `gbp`.`port_orig`)))
    where
        (`gbp`.`globalchargelcl_id` = `gbl`.`id`)) AS `port_orig`,
    (
    select
        group_concat(distinct `har`.`display_name` separator ' | ')
    from
        (`globalcharports_lcl` `gbp`
    join `harbors` `har` on
        ((`har`.`id` = `gbp`.`port_dest`)))
    where
        (`gbp`.`globalchargelcl_id` = `gbl`.`id`)) AS `port_dest`,
    (
    select
        group_concat(distinct `coun`.`name` separator ' | ')
    from
        (`globalcharcountry_lcl` `gbCD`
    join `countries` `coun` on
        ((`coun`.`id` = `gbCD`.`country_orig`)))
    where
        (`gbCD`.`globalchargelcl_id` = `gbl`.`id`)) AS `country_orig`,
    (
    select
        group_concat(distinct `counD`.`name` separator ' | ')
    from
        (`globalcharcountry_lcl` `gbCD`
    join `countries` `counD` on
        ((`counD`.`id` = `gbCD`.`country_dest`)))
    where
        (`gbCD`.`globalchargelcl_id` = `gbl`.`id`)) AS `country_dest`,
    (
    select
        group_concat(distinct `carr`.`name` separator ' | ')
    from
        (`globalcharcarriers_lcl` `gbC`
    join `carriers` `carr` on
        ((`carr`.`id` = `gbC`.`carrier_id`)))
    where
        (`gbC`.`globalchargelcl_id` = `gbl`.`id`)) AS `carrier`,
    `sg`.`name` AS `surcharges`,
    `td`.`description` AS `typedestiny`,
    `ct`.`name` AS `calculationtype`,
    `gbl`.`ammount` AS `ammount`,
    `gbl`.`minimum` AS `minimum`,
    `gbl`.`validity` AS `validity`,
    `gbl`.`expire` AS `expire`,
    `cy`.`alphacode` AS `currency`,
    `cmpu`.`name` AS `company_user`,
    `cmpu`.`id` AS `company_user_id`
from
    (((((`globalcharges_lcl` `gbl`
join `surcharges` `sg` on
    ((`gbl`.`surcharge_id` = `sg`.`id`)))
join `typedestiny` `td` on
    ((`gbl`.`typedestiny_id` = `td`.`id`)))
join `calculationtypelcl` `ct` on
    ((`gbl`.`calculationtypelcl_id` = `ct`.`id`)))
join `currency` `cy` on
    ((`gbl`.`currency_id` = `cy`.`id`)))
join `company_users` `cmpu` on
    ((`gbl`.`company_user_id` = `cmpu`.`id`))));


-- ebdb.views_localcharges source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_localcharges` AS (
select
    `lc`.`id` AS `id`,
    `lc`.`contract_id` AS `contract_id`,
    `sr`.`name` AS `surcharge`,
    (
    select
        group_concat(distinct `har`.`display_name` separator ', ')
    from
        (`localcharports` `lcP`
    join `harbors` `har` on
        ((`har`.`id` = `lcP`.`port_orig`)))
    where
        (`lcP`.`localcharge_id` = `lc`.`id`)) AS `port_orig`,
    (
    select
        group_concat(distinct `har`.`display_name` separator ', ')
    from
        (`localcharports` `lcP`
    join `harbors` `har` on
        ((`har`.`id` = `lcP`.`port_dest`)))
    where
        (`lcP`.`localcharge_id` = `lc`.`id`)) AS `port_dest`,
    (
    select
        group_concat(distinct `coun`.`name` separator ', ')
    from
        (`localcharcountry` `lcCO`
    join `countries` `coun` on
        ((`coun`.`id` = `lcCO`.`country_orig`)))
    where
        (`lcCO`.`localcharge_id` = `lc`.`id`)) AS `country_orig`,
    (
    select
        group_concat(distinct `counD`.`name` separator ', ')
    from
        (`localcharcountry` `lcCD`
    join `countries` `counD` on
        ((`counD`.`id` = `lcCD`.`country_dest`)))
    where
        (`lcCD`.`localcharge_id` = `lc`.`id`)) AS `country_dest`,
    `td`.`description` AS `changetype`,
    (
    select
        group_concat(distinct `carr`.`name` separator ', ')
    from
        (`localcharcarriers` `lcC`
    join `carriers` `carr` on
        ((`carr`.`id` = `lcC`.`carrier_id`)))
    where
        (`lcC`.`localcharge_id` = `lc`.`id`)) AS `carrier`,
    `ctype`.`name` AS `calculation_type`,
    `cur`.`alphacode` AS `currency`,
    `lc`.`ammount` AS `ammount`
from
    ((((`localcharges` `lc`
join `surcharges` `sr` on
    ((`sr`.`id` = `lc`.`surcharge_id`)))
join `typedestiny` `td` on
    ((`td`.`id` = `lc`.`typedestiny_id`)))
join `currency` `cur` on
    ((`cur`.`id` = `lc`.`currency_id`)))
join `calculationtype` `ctype` on
    ((`ctype`.`id` = `lc`.`calculationtype_id`))));


-- ebdb.views_localcharges_ids source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_localcharges_ids` AS (
select
    `lc`.`id` AS `id`,
    `lc`.`contract_id` AS `contract_id`,
    `sr`.`id` AS `surcharge`,
    (
    select
        group_concat(distinct `har`.`id` separator ', ')
    from
        (`localcharports` `lcP`
    join `harbors` `har` on
        ((`har`.`id` = `lcP`.`port_orig`)))
    where
        (`lcP`.`localcharge_id` = `lc`.`id`)) AS `port_orig`,
    (
    select
        group_concat(distinct `har`.`id` separator ', ')
    from
        (`localcharports` `lcP`
    join `harbors` `har` on
        ((`har`.`id` = `lcP`.`port_dest`)))
    where
        (`lcP`.`localcharge_id` = `lc`.`id`)) AS `port_dest`,
    (
    select
        group_concat(distinct `coun`.`id` separator ', ')
    from
        (`localcharcountry` `lcCO`
    join `countries` `coun` on
        ((`coun`.`id` = `lcCO`.`country_orig`)))
    where
        (`lcCO`.`localcharge_id` = `lc`.`id`)) AS `country_orig`,
    (
    select
        group_concat(distinct `counD`.`id` separator ', ')
    from
        (`localcharcountry` `lcCD`
    join `countries` `counD` on
        ((`counD`.`id` = `lcCD`.`country_dest`)))
    where
        (`lcCD`.`localcharge_id` = `lc`.`id`)) AS `country_dest`,
    `td`.`id` AS `changetype`,
    (
    select
        group_concat(distinct `carr`.`id` separator ', ')
    from
        (`localcharcarriers` `lcC`
    join `carriers` `carr` on
        ((`carr`.`id` = `lcC`.`carrier_id`)))
    where
        (`lcC`.`localcharge_id` = `lc`.`id`)) AS `carrier`,
    `ctype`.`id` AS `calculation_type`,
    `cur`.`id` AS `currency`,
    `lc`.`ammount` AS `ammount`
from
    ((((`localcharges` `lc`
join `surcharges` `sr` on
    ((`sr`.`id` = `lc`.`surcharge_id`)))
join `typedestiny` `td` on
    ((`td`.`id` = `lc`.`typedestiny_id`)))
join `currency` `cur` on
    ((`cur`.`id` = `lc`.`currency_id`)))
join `calculationtype` `ctype` on
    ((`ctype`.`id` = `lc`.`calculationtype_id`))));


-- ebdb.views_rates source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_rates` AS (
select
    `ra`.`id` AS `id`,
    `cont`.`id` AS `contract_id`,
    `har_orig`.`display_name` AS `port_orig`,
    `har_dest`.`display_name` AS `port_dest`,
    `car`.`name` AS `carrier`,
    `ra`.`twuenty` AS `twuenty`,
    `ra`.`forty` AS `forty`,
    `ra`.`fortyhc` AS `fortyhc`,
    `ra`.`fortynor` AS `fortynor`,
    `ra`.`fortyfive` AS `fortyfive`,
    `curr`.`alphacode` AS `currency`,
    `sh`.`name` AS `schedule_type`,
    `ra`.`transit_time` AS `transit_time`,
    `ra`.`via` AS `via`
from
    ((((((`rates` `ra`
join `harbors` `har_orig` on
    ((`har_orig`.`id` = `ra`.`origin_port`)))
join `harbors` `har_dest` on
    ((`har_dest`.`id` = `ra`.`destiny_port`)))
join `carriers` `car` on
    ((`car`.`id` = `ra`.`carrier_id`)))
left join `schedule_type` `sh` on
    ((`ra`.`schedule_type_id` = `sh`.`id`)))
join `currency` `curr` on
    ((`curr`.`id` = `ra`.`currency_id`)))
join `contracts` `cont` on
    ((`cont`.`id` = `ra`.`contract_id`))));


-- ebdb.views_rates_lcl source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `views_rates_lcl` AS (
select
    `ra`.`id` AS `id`,
    `cont`.`id` AS `contract_id`,
    `har_orig`.`display_name` AS `port_orig`,
    `har_dest`.`display_name` AS `port_dest`,
    `car`.`name` AS `carrier`,
    `ra`.`uom` AS `uom`,
    `ra`.`minimum` AS `minimum`,
    `curr`.`alphacode` AS `currency`,
    `sh`.`name` AS `schedule_type`,
    `ra`.`transit_time` AS `transit_time`,
    `ra`.`via` AS `via`
from
    ((((((`rates_lcl` `ra`
join `harbors` `har_orig` on
    ((`har_orig`.`id` = `ra`.`origin_port`)))
join `harbors` `har_dest` on
    ((`har_dest`.`id` = `ra`.`destiny_port`)))
join `carriers` `car` on
    ((`car`.`id` = `ra`.`carrier_id`)))
join `currency` `curr` on
    ((`curr`.`id` = `ra`.`currency_id`)))
left join `schedule_type` `sh` on
    ((`ra`.`schedule_type_id` = `sh`.`id`)))
join `contracts_lcl` `cont` on
    ((`cont`.`id` = `ra`.`contractlcl_id`))));