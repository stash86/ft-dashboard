-- R2Bot PostgreSQL Database Schema
-- This script will run automatically when the PostgreSQL container starts

-- Enable required extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Create strategies table (replaces MongoDB strategies collection)
CREATE TABLE IF NOT EXISTS strategies (
    id SERIAL PRIMARY KEY,
    strategy_name VARCHAR(255) UNIQUE NOT NULL,
    data JSONB NOT NULL,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better JSONB query performance
CREATE INDEX IF NOT EXISTS idx_strategies_name ON strategies ((data->>'strategy'));
CREATE INDEX IF NOT EXISTS idx_strategies_status ON strategies ((data->>'status'));
CREATE INDEX IF NOT EXISTS idx_strategies_ip ON strategies ((data->>'ip'));
CREATE INDEX IF NOT EXISTS idx_strategies_performance ON strategies USING GIN ((data->'performance'));
CREATE INDEX IF NOT EXISTS idx_strategies_trades ON strategies USING GIN ((data->'trades'));

-- Create function to update the updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers to automatically update updated_at
CREATE TRIGGER update_strategies_updated_at
    BEFORE UPDATE ON strategies
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Grant permissions dynamically to the current user
-- This automatically uses whatever user is specified in POSTGRES_USER environment variable
DO $$
BEGIN
    -- Grant permissions to the current user (from POSTGRES_USER env var)
    EXECUTE format('GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO %I', current_user);
    EXECUTE format('GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO %I', current_user);
    EXECUTE format('GRANT USAGE ON SCHEMA public TO %I', current_user);

    -- Also ensure the user can create tables and sequences
    EXECUTE format('GRANT CREATE ON SCHEMA public TO %I', current_user);

    RAISE NOTICE 'Granted permissions to user: %', current_user;
END $$;
