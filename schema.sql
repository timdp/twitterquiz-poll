CREATE TABLE "votes" (
  "user_id" integer NOT NULL,
  "screen_name" varchar(20) DEFAULT NULL,
  "vote" integer NOT NULL,
  "when" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ("user_id")
);