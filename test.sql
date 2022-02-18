/*Querys Eliminacion globalcharport */
/* No hay resultados */
delete from
    global_char_port_exceptions
where
    globalcharge_id in (
        SELECT
            *
        FROM
            (
                SELECT
                    DISTINCT (g.id)
                from
                    globalcharges g
                    inner join global_char_port_exceptions gcpe on g.id = gcpe.globalcharge_id
                    inner join globalcharport g2 on g.id = g2.globalcharge_id
                where
                    g2.port_orig != 1485
                    and g2.port_dest != 1485
            ) as id
    )
delete from
    global_char_port_exceptions
where
    globalcharge_id in (
        SELECT
            *
        FROM
            (
                SELECT
                    DISTINCT (g.id)
                from
                    globalcharges g
                    inner join global_char_port_exceptions gcpe2 on g.id = gcpe2.globalcharge_id
                    inner join global_char_country_ports gccp on g.id = gccp.globalcharge_id
                where
                    gccp.port_dest != 1485
                    AND gccp.country_orig != 250
            ) as id
    )
    /* No hay resultados */
delete from
    global_char_port_exceptions
where
    globalcharge_id in (
        SELECT
            *
        FROM
            (
                SELECT
                    DISTINCT (g.id)
                from
                    globalcharges g
                    inner join global_char_port_exceptions gcpe2 on g.id = gcpe2.globalcharge_id
                    inner join global_char_port_countries gcpc on g.id = gcpc.globalcharge_id
                where
                    gcpc.country_dest != 250
                    and gcpc.port_orig != 1485
            ) as id
    )
delete from
    global_char_country_exceptions
where
    globalcharge_id in (
        SELECT
            *
        FROM
            (
                SELECT
                    DISTINCT (g.id)
                from
                    globalcharges g
                    inner join global_char_country_exceptions gcpe on g.id = gcpe.globalcharge_id
                    inner join globalcharcountry g2 on g.id = g2.globalcharge_id
                where
                    g2.country_orig != 250
                    and g2.country_dest != 250
            ) as id
    )
delete from
    global_char_country_exceptions
where
    globalcharge_id in (
        SELECT
            *
        FROM
            (
                SELECT
                    DISTINCT (g.id)
                from
                    globalcharges g
                    inner join global_char_country_exceptions gcpe on g.id = gcpe.globalcharge_id
                    inner join global_char_port_countries gcpc on g.id = gcpc.globalcharge_id
                where
                    gcpc.port_orig != 1485
                    AND gcpc.country_dest != 250
            ) as id
    )
delete from
    global_char_country_exceptions
where
    globalcharge_id in (
        SELECT
            *
        FROM
            (
                SELECT
                    DISTINCT (g.id)
                from
                    globalcharges g
                    inner join global_char_country_exceptions gcpe2 on g.id = gcpe2.globalcharge_id
                    inner join global_char_country_ports gcpc on g.id = gcpc.globalcharge_id
                where
                    gcpc.port_dest != 1485
                    AND gcpc.country_orig != 250
            ) as id
    )