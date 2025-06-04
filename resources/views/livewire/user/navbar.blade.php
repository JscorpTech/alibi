
    <nav class="hidden lg:flex flex-0" aria-labelledby="mainmenulabel">
        <span id="mainmenulabel" class="sr-only">Main Menu</span>


        <a href="{{route("home")}}" style="cursor: pointer"
           class="header_nav_link py-[2px] px-[0.75rem]  text-xs uppercase text-black hover:text-gray-600 cursor-default h-full flex items-center select-none"
           @click="removeAttributes($event);" tabindex="0">
            {{__("Home")}}
        </a>

    </nav>
