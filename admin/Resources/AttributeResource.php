<?php

namespace Neon\Admin\Resources;

use Camya\Filament\Forms\Components\TitleWithSlugInput;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Neon\Admin\Resources\AttributeResource\Pages;
use Neon\Admin\Resources\AttributeResource\RelationManagers;
use Neon\Attributable\Models\Attribute;

class AttributeResource extends Resource
{
  protected static ?int $navigationSort = 98;

  protected static ?string $model = Attribute::class;
  
  protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';
  
  protected static ?string $activeNavigationIcon = 'heroicon-s-code-bracket-square';

  public static function getNavigationLabel(): string
  {
    return trans('neon-admin::admin.resources.attributables.title');
  }

  public static function getNavigationGroup(): string
  {
    return trans('neon-admin::admin.navigation.settings');
  }

  public static function getModelLabel(): string
  {
    return trans('neon-admin::admin.models.attribute');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('neon-admin::admin.models.attributes');
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Select::make('class')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.class.label'))
          ->native(false)
          ->options(function ():array {

            /**
             * @var array $result Return result of the Closure.
             */
            $result     = [];

            /**
             * @var Iterable $resources All the registered Filament resources.
             */
            $resources  = app('filament')->getResources();
            
            foreach ($resources as $resource)
            {
              /** Create the resource, to get the related model.
               */
              $_r = new $resource();

              /** If getting  */
              if (in_array('Neon\Attributable\Models\Traits\Attributable', class_uses_recursive($_r->getModel())))
              {
                $result[$_r->getModel()] = $_r::getNavigationLabel();
              }
            }
            return $result;
          }),
        // Fieldset::make(__('neon-admin::admin.resources.attributables.form.fieldset.name'))
        //   ->schema([
        //     TextInput::make('name')
        //       ->label()
        //       ->required()
        //       ->maxLength(255),
        //     TextInput::make('slug')
        //       ->label()
        //       ->required()
        //       ->maxLength(255)
        //       ->alphaDash(),
        //     Hidden::make('is_slug_changed_manually')
        //       ->default(false)
        //       ->dehydrated(false),
        //   ])
        //   ->columns(2),
        TitleWithSlugInput::make(
          fieldTitle: 'name',
          fieldSlug: 'slug',
          titleLabel: __('neon-admin::admin.resources.attributables.form.fields.name.label'),
          slugLabel: '',
          urlPath: '',
          urlHostVisible: false,
          urlVisitLinkVisible: false,
          slugSlugifier: fn ($string) => preg_replace( '/-/i', '_', Str::slug($string)),
        ),
        Select::make('cast_as')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.cast_as.label'))
          ->searchable()
          ->options([
            'string'  => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.string'),
            'integer' => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.integer'),
            'float'   => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.float'),
            'boolean' => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.boolean'),
            'array'   => __('neon-admin::admin.resources.attributables.form.fields.cast_as.options.array'),
          ])
          ->helperText(__('neon-admin::admin.resources.attributables.form.fields.cast_as.help'))
          ->columns(2),
        Select::make('field')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.field.label'))
          ->native(false)
          ->options([
            'text'    => __('neon-admin::admin.resources.attributables.form.fields.field.options.text'),
            'boolean' => __('neon-admin::admin.resources.attributables.form.fields.field.options.boolean'),
            'text'    => __('neon-admin::admin.resources.attributables.form.fields.field.options.select'),
          ]),
        Select::make('rules')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.rules.label'))
          ->multiple()
          ->searchable()
          ->options([
            'activeUrl' => __('neon-admin::admin.resources.attributables.form.fields.rules.options.activeUrl'), // 'URL',
            'alpha'     => __('neon-admin::admin.resources.attributables.form.fields.rules.options.alpha'), // 'Csak betűk',
            'alphaDash' => __('neon-admin::admin.resources.attributables.form.fields.rules.options.alphaDash'), // 'Csak betűk, számok és kötőjel és aláhúzásjel',
            'alphaNum'  => __('neon-admin::admin.resources.attributables.form.fields.rules.options.alphaNum'), // 'Csak betűk és számok',
            'required'  => __('neon-admin::admin.resources.attributables.form.fields.rules.options.required'), // 'Kötelező kitölteni',
            'ascii'     => __('neon-admin::admin.resources.attributables.form.fields.rules.options.ascii'), // 'Csak ASCII karakterek'
            'tel'       => __('neon-admin::admin.resources.attributables.form.fields.rules.options.tel')
          ]),
        KeyValue::make('params')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.params.label'))
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label(__('neon-admin::admin.resources.attributables.form.fields.name.label'))
          ->description(fn (Attribute $record): string => $record->class)
          ->searchable(),
      ])
      ->filters([
        Tables\Filters\TrashedFilter::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make()
          ->slideOver(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ManageAttributes::route('/'),
    ];
  }
}
