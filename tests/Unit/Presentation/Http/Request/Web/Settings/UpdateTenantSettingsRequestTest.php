<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Web\Settings\UpdateTenantSettingsRequest;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;

/**
 * @return array<string, array<int, string|Closure>>
 */
function updateTenantSettingsRules(): array
{
    return (new UpdateTenantSettingsRequest)->rules();
}

/**
 * @return Closure(string, mixed, Closure): void
 */
function displayTimezoneRuleClosure(): Closure
{
    /** @var list<string|Closure> $ruleList */
    $ruleList = updateTenantSettingsRules()['display_timezone'];
    $custom = $ruleList[3];
    if (! $custom instanceof Closure) {
        throw new RuntimeException('Expected display_timezone custom rule to be a Closure.');
    }

    return $custom;
}

/** @param array<string, mixed> $data */
function tenantSettingsRulesValidator(array $data): Validator
{
    $translator = new Translator(new ArrayLoader, 'en');
    $factory = new Factory($translator);

    return $factory->make($data, updateTenantSettingsRules());
}

it('allows null and empty string in the display_timezone custom rule closure', function (): void {
    $closure = displayTimezoneRuleClosure();

    $fail = static function (string $message): never {
        throw new RuntimeException($message);
    };

    $closure('display_timezone', null, $fail);
    $closure('display_timezone', '', $fail);

    expect(true)->toBeTrue();
});

it('passes validation factory with a valid IANA timezone', function (): void {
    $validator = tenantSettingsRulesValidator([
        'name' => 'Tenant',
        'display_timezone' => 'UTC',
    ]);

    expect($validator->passes())->toBeTrue();
});
