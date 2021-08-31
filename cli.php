<?php

namespace ArtARTs36\DbCreator\Cli
{
    const ENV_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'db-creator.env';

    use ArtARTs36\DbCreator\Access;
    use ArtARTs36\DbCreator\Creator;
    use ArtARTs36\DbCreator\SystemFactory;
    use ArtARTs36\EnvEditor\Editor;
    use ArtARTs36\Str\Facade\Str;
    use JetBrains\PhpStorm\ArrayShape;

    #[ArrayShape(['host' => 'string', 'port' => 'int', 'user' => 'string', 'password' => 'string'])]
    function get_config_from_environment(string $systemName): array
    {
        return array_change_key_case(
            Editor::load(ENV_FILE_PATH)->getVariablesByPrefix(Str::toUpper($systemName) . '_', true),
            CASE_LOWER
        );
    }

    function dump_env_file(): void
    {
        $env = Editor::create(ENV_FILE_PATH);

        foreach (array_keys(SystemFactory::ALIASES) as $systemName) {
            $prefix = Str::toUpper($systemName);

            $env
                ->set($prefix . '_HOST', 'localhost')
                ->set($prefix . '_PORT', '5432')
                ->set($prefix . '_USER', 'root')
                ->set($prefix . '_PASSWORD', 'root');
        }

        $env->save();

        echo "Dumped environment variables into ". ENV_FILE_PATH;
    }

    function show_help(): void
    {
        echo <<<HTML
1. "db-creator dump-env" - create example env file
2. "db-creator locate-env" - show environment file path
3. "db-creator create pgsql test-db-name" - create pgsql database
4. "db-creator create mysql test-db-name" - create mysql database
HTML;
    }

    function create_database(array $arguments): void
    {
        [$systemName, $dbName] = $arguments;

        $env = get_config_from_environment($systemName);
        $credentials = Access::make($env['user'], $env['password'], $env['port'], $env['host']);

        Creator::create($credentials, $systemName, $dbName);

        echo "Created $systemName database \"$dbName\"";
    }

    function locate_env(): void
    {
        echo 'Environment file path: '. ENV_FILE_PATH;

        if (! file_exists(ENV_FILE_PATH)) {
            echo "\n" . 'File not exists! You can create through "db-creator dump-env"';
        }
    }
}
