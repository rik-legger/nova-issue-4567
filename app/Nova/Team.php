<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Team extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Team::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return array(
            ID::make()->sortable(),

            BelongsTo::make('User')
                //->searchable() // separate issue, when using searchable it will not update the selection when match found.
                ->dependsOn(['name'], function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                    if (isset($formData['name'])) {

                        $user = \App\Models\User::query()
                            ->where('name', 'LIKE', "%{$formData['name']}%")
                            ->first();

                        if ($user) {
                            $field->default(fn () => $user->id)
                                ->help(sprintf('Found match for user with id #%s', $user->id));
                        } else {
                            $field->help(sprintf('Searching for user with name: %s', $formData['name']));
                        }
                    }
                }),

            Text::make('Name')
                ->help('Input user name to trigger depends on functionality.')
                ->fillUsing(fn () => null)
                ->onlyOnForms(),
        );
    }
}
