<?php namespace Distilleries\Expendable\Forms\Role;

use Distilleries\Expendable\Helpers\StaticLabel;
use Distilleries\FormBuilder\FormValidator;

class RoleForm extends FormValidator {

    public static $rules = [
        'label'            => 'required',
        'initials'           => 'required|unique:roles',
        'overide_permission' => 'integer'
    ];

    public function buildForm()
    {
        $this
            ->add('id', 'hidden')
            ->add('label', 'text', [
                'validation' => 'required',
                'label'      => trans('expendable::form.label')
            ])
            ->add('initials', 'text', [
                'validation' => 'required',
                'label'      => trans('expendable::form.initials')
            ])
            ->add('overide_permission', 'choice', [
                'choices'     => StaticLabel::yesNo(),
                'empty_value' => '-',
                'validation'  => 'required',
                'label'       => trans('expendable::form.allow_automatically_all_permission')
            ]);

        $this->addDefaultActions();
    }
}