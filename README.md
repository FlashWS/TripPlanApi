# TriPlan API

CREATE SCHEMA postgis;
CREATE EXTENSION postgis SCHEMA postgis;
ALTER DATABASE trip_plan SET search_path=public,postgis;
