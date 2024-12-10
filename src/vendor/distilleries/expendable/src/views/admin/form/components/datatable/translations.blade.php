<ul class="list-unstyled list-inline">
    @foreach($languages as $language)
     <?php 
        $lang = explode('_', $language->iso);
        $iso = strtolower($lang[0]);
        $flag = isset($lang[1]) ? strtolower($lang[1]) : strtolower($lang[0]);
     ?>

        <li>
            @if(in_array($iso,array_keys($translations)))
                <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($route.'getEdit',$translations[$iso])) }}" class="edit-flag-link">
                    <i class="glyphicon glyphicon-pencil"></i>
                    <br /><span class="flags-sprite flags-{{$flag}}"></span>
                </a>

            @else
                <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($route.'getTranslation',[$iso, $data['id']])) }}" class="edit-flag-link">
                    <span class="flags-sprite flags-{{$flag}}"></span>
                </a>
            @endif
        </li>
    @endforeach
</ul>
