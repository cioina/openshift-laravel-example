<ul class="list-unstyled list-inline">
    @foreach($languages as $language)
     <?php 
        $lang = explode('_', $language->iso);
        $iso = strtolower($lang[0]);
        $flag = isset($lang[1]) ? strtolower($lang[1]) : strtolower($lang[0]);
     ?>

        <li>
            @if(in_array($iso,array_keys($translations)))
                <a href="{{ \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($route.'getView', $translations[$iso])) }}" class="edit-flag-link">
                    <span class="flags-sprite flags-{{$flag}}"></span>
                </a>
            @endif
        </li>
    @endforeach
</ul>
