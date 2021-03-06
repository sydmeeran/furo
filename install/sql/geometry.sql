/* tb 1 */
CREATE TABLE `polygons` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `polygon` POLYGON NOT NULL,
    `country` VARCHAR(50) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    SPATIAL INDEX `polygon` (`polygon`)
) CHARSET='utf8mb4' COLLATE='utf8mb4_unicode_ci' ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO `polygons` (`country`, `polygon`) VALUES ('Thailand', ST_GEOMFROMTEXT('POLYGON((102.1728516 6.1842462,101.6894531 5.7253114,101.1401367 5.6815837,101.1181641 6.2497765,100.1074219 6.4899833,96.3281250 6.4244835,96.1083984 9.8822755,98.7670898 10.1419317,99.5800781 11.8243415,98.2177734 15.1569737,98.9868164 16.3201395,97.4267578 18.4587681,98.1079102 19.7253422,99.0087891 19.7460242,100.2612305 20.2828087,100.4809570 19.4769502,101.2060547 19.4147924,100.8544922 17.4135461,102.0849609 17.9996316,102.8320313 17.7696122,103.3593750 18.3545255,104.7875977 17.4554726,104.6337891 16.4676947,105.5126953 15.6018749,105.2270508 14.3069695,102.9858398 14.2643831,102.3486328 13.5819209,103.0297852 11.0059045,103.6669922 8.5592939,102.1728516 6.1842462))'));
SELECT polygon.id FROM polygons WHERE ST_CONTAINS(polygon, POINT(101.490104,13.03887));


/* tb 2 */
CREATE TABLE IF NOT EXISTS `locations_flat`(
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100),
    `position` POINT NOT NULL SRID 0
);
ALTER TABLE `locations_flat` ADD SPATIAL INDEX(`position`);
SHOW COLUMNS FROM `locations_flat`;

INSERT INTO `locations_flat`(`name`, `position`) VALUES
( 'point_1', ST_GeomFromText( 'POINT( 1 1 )', 0 ) ),
( 'point_2', ST_GeomFromText( 'POINT( 2 2 )', 0 ) ),
( 'point_3', ST_GeomFromText( 'POINT( 3 3 )', 0 ) );


/* tb 3 */
CREATE TABLE IF NOT EXISTS `locations_earth`(
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100),
    `position` POINT NOT NULL SRID 4326,
    SPATIAL INDEX(`position`)
);
SHOW COLUMNS FROM `locations_earth`;

INSERT INTO `locations_earth`(`name`, `position`) VALUES
('point_1', ST_GeomFromText('POINT(0.09000 0.18000)', 4326)),
('point_2', ST_GeomFromText('POINT(0.18000 0.36000)', 4326)),
('point_3', ST_GeomFromText('POINT(0.27000 0.54000)', 4326));


/* Distance */
SET @lotus_temple := ST_GeomFromText( 'POINT(28.553298 77.259221)', 4326, 'axis-order=lat-long' );
SET @india_gate := ST_GeomFromText( 'POINT(28.612849 77.229883)', 4326 );
SELECT ST_Latitude( @lotus_temple ) AS `lat_lotus_temple`,
ST_Longitude( @lotus_temple ) AS `long_lotus_temple`,
ST_Latitude( @india_gate ) AS `lat_india_gate`,
ST_Longitude( @india_gate ) AS `long_india_gate`,
ST_Distance_Sphere( @lotus_temple, @india_gate ) AS `distance`;


/* All */
INSERT INTO `polygons` (`country`, `polygon`) VALUES ('Thailand', ST_GEOMFROMTEXT('POLYGON((102.1728516 6.1842462,101.6894531 5.7253114,101.1401367 5.6815837,101.1181641 6.2497765,100.1074219 6.4899833,96.3281250 6.4244835,96.1083984 9.8822755,98.7670898 10.1419317,99.5800781 11.8243415,98.2177734 15.1569737,98.9868164 16.3201395,97.4267578 18.4587681,98.1079102 19.7253422,99.0087891 19.7460242,100.2612305 20.2828087,100.4809570 19.4769502,101.2060547 19.4147924,100.8544922 17.4135461,102.0849609 17.9996316,102.8320313 17.7696122,103.3593750 18.3545255,104.7875977 17.4554726,104.6337891 16.4676947,105.5126953 15.6018749,105.2270508 14.3069695,102.9858398 14.2643831,102.3486328 13.5819209,103.0297852 11.0059045,103.6669922 8.5592939,102.1728516 6.1842462))'));
SELECT polygon.id FROM polygons WHERE ST_CONTAINS(polygon, POINT(101.490104,13.03887));
SELECT ST_AsGeoJSON(polygon) as polygon,id FROM polygons WHERE ST_CONTAINS(polygon, POINT(101.490104,13.03887));
SELECT AsText(polygon) as polygon,id FROM polygons WHERE ST_CONTAINS(polygon, POINT(101.490104,13.03887));
SELECT ST_Contains(PolygonFromText('POLYGON((9.190586853 45.464518970,9.190602686 45.463993916,9.191640825 45.464647090,9.191622331 45.464506215,9.190586853 45.464518970))'), PointFromText("POINT(10 42)");
SELECT ST_X(@point) AS latitude;
SELECT ST_Y(@point) AS longitude;
SELECT ST_Distance(@point, @location) AS `distance`

/*
CONCAT('Point(',location,')')
*/