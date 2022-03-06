<div class="profile__group">
    @for($i=1; $i<5; $i++)
        <a href="#" class="profile">
            <img
                src="{{ asset("images/profile__photo{$i}.png") }}"
                alt="profile photo"
                width="32"
                height="auto"
                class="photo"
            />
            <span class="profile__tooltip"> Pablo Picasso </span>
        </a>
    @endfor
</div>
