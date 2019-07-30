SELECT 
        `gc`.`id` AS `id`,
        `sr`.`name` AS `charge`,
        `td`.`description` AS `charge_type`,
        `ctype`.`name` AS `calculation_type`,
        (SELECT 
                GROUP_CONCAT(DISTINCT `har`.`code`
                        SEPARATOR ', ')
            FROM
                (`globalcharport` `gcP`
                JOIN `harbors` `har` ON ((`har`.`id` = `gcP`.`port_orig`)))
            WHERE
                (`gcP`.`globalcharge_id` = `gc`.`id`)) AS `origin_port`,
        (SELECT 
                GROUP_CONCAT(DISTINCT `har`.`code`
                        SEPARATOR ', ')
            FROM
                (`globalcharport` `gcP`
                JOIN `harbors` `har` ON ((`har`.`id` = `gcP`.`port_dest`)))
            WHERE
                (`gcP`.`globalcharge_id` = `gc`.`id`)) AS `destination_port`,
        (SELECT 
                GROUP_CONCAT(DISTINCT `coun`.`name`
                        SEPARATOR ', ')
            FROM
                (`globalcharcountry` `gcCO`
                JOIN `countries` `coun` ON ((`coun`.`id` = `gcCO`.`country_orig`)))
            WHERE
                (`gcCO`.`globalcharge_id` = `gc`.`id`)) AS `origin_country`,
        (SELECT 
                GROUP_CONCAT(DISTINCT `counD`.`name`
                        SEPARATOR ', ')
            FROM
                (`globalcharcountry` `gcCD`
                JOIN `countries` `counD` ON ((`counD`.`id` = `gcCD`.`country_dest`)))
            WHERE
                (`gcCD`.`globalcharge_id` = `gc`.`id`)) AS `destination_country`,
        (SELECT 
                GROUP_CONCAT(DISTINCT `carr`.`name`
                        SEPARATOR ', ')
            FROM
                (`globalcharcarrier` `gcC`
                JOIN `carriers` `carr` ON ((`carr`.`id` = `gcC`.`carrier_id`)))
            WHERE
                (`gcC`.`globalcharge_id` = `gc`.`id`)) AS `carrier`,
        (SELECT 
                GROUP_CONCAT(DISTINCT `carr`.`uncode`
                        SEPARATOR ', ')
            FROM
                (`globalcharcarrier` `gcC`
                JOIN `carriers` `carr` ON ((`carr`.`id` = `gcC`.`carrier_id`)))
            WHERE
                (`gcC`.`globalcharge_id` = `gc`.`id`)) AS `carriers`,
        `gc`.`ammount` AS `amount`,
        `cur`.`alphacode` AS `currency_code`,
        `gc`.`validity` AS `valid_from`,
        `gc`.`expire` AS `valid_until`,
        `gc`.`company_user_id` AS `company_user_id`,
        `cmpu`.`name` AS `company_user`
    FROM
        (((((`globalcharges` `gc`
        JOIN `surcharges` `sr` ON ((`sr`.`id` = `gc`.`surcharge_id`)))
        JOIN `typedestiny` `td` ON ((`td`.`id` = `gc`.`typedestiny_id`)))
        JOIN `currency` `cur` ON ((`cur`.`id` = `gc`.`currency_id`)))
        JOIN `calculationtype` `ctype` ON ((`ctype`.`id` = `gc`.`calculationtype_id`)))
        JOIN `company_users` `cmpu` ON ((`cmpu`.`id` = `gc`.`company_user_id`)))
        
        
        
        
        
        CREATE DEFINER=`cargofive`@`%` PROCEDURE `select_globalcharge_adm`(IN comp_id int)
SELECT gb.id, (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM globalcharport gbp INNER JOIN harbors har on har.id = gbp.port_orig WHERE gbp.globalcharge_id = gb.id ) as port_orig , (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM globalcharport gbp INNER JOIN harbors har on har.id = gbp.port_dest WHERE gbp.globalcharge_id = gb.id ) as port_dest, (SELECT GROUP_CONCAT(DISTINCT(coun.name) SEPARATOR ', ') FROM globalcharcountry gbCD INNER JOIN countries coun on coun.id = gbCD.country_orig WHERE gbCD.globalcharge_id = gb.id ) as country_orig , (SELECT GROUP_CONCAT(DISTINCT(counD.name) SEPARATOR ', ') FROM globalcharcountry gbCD INNER JOIN countries counD on counD.id = gbCD.country_dest WHERE gbCD.globalcharge_id = gb.id ) as country_dest , (SELECT GROUP_CONCAT(DISTINCT(carr.name) SEPARATOR ', ') FROM globalcharcarrier gbC INNER JOIN carriers carr on carr.id = gbC.carrier_id WHERE gbC.globalcharge_id = gb.id ) as carrier, sg.name as surcharges, td.description as typedestiny, ct.name as calculationtype, gb.ammount, gb.validity,gb.expire, cy.alphacode AS currency, cmpu.name as company_user, gb.account_importation_globalcharge_id FROM globalcharges gb INNER JOIN surcharges sg ON gb.surcharge_id = sg.id INNER JOIN typedestiny td ON gb.typedestiny_id = td.id INNER JOIN calculationtype ct ON gb.calculationtype_id = ct.id INNER JOIN currency cy ON gb.currency_id = cy.id INNER JOIN company_users cmpu ON gb.company_user_id = cmpu.id WHERE gb.account_importation_globalcharge_id = account_id