CREATE TABLE ezadigital.meta
(
    id serial NOT NULL,
    nombre character varying(63) NOT NULL,
    descripcion character varying(255),
    id_usuario integer,
    fecha_creacion timestamp without time zone,
    fecha_actualizacion timestamp without time zone,
    fecha_eliminacion timestamp without time zone,
    status smallint NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

CREATE TABLE ezadigital.meta_usuario
(
    id serial NOT NULL,
    id_meta integer NOT NULL,
    id_usuario integer NOT NULL,
    valor character varying(31),
    fecha date,
    fecha_creacion timestamp without time zone,
    fecha_actualizacion timestamp without time zone,
    fecha_eliminacion timestamp without time zone,
    status smallint NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    FOREIGN KEY (id_meta)
        REFERENCES ezadigital.meta (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE CASCADE
        NOT VALID,
    FOREIGN KEY (id_usuario)
        REFERENCES public.usuario (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE CASCADE
        NOT VALID
);

INSERT INTO ezadigital.meta
    (nombre, descripcion, fecha_creacion, fecha_actualizacion, status)
VALUES
    ('Colocación', 'Meta preestablecida', '2020-09-04 16:26:09', '2020-09-04 16:26:09', 0),
    ('Crecimiento', 'Meta preestablecida', '2020-09-04 16:26:09', '2020-09-04 16:26:09', 0),
    ('Número de Clientes', 'Meta preestablecida', '2020-09-04 16:26:09', '2020-09-04 16:26:09', 0);


/* ======================================================================================================= */


