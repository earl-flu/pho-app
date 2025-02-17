<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
  laravelVersion: {
    type: String,
    required: true,
  },
  phpVersion: {
    type: String,
    required: true,
  },
});

function handleImageError() {
  document.getElementById("screenshot-container")?.classList.add("!hidden");
  document.getElementById("docs-card")?.classList.add("!row-span-1");
  document.getElementById("docs-card-content")?.classList.add("!flex-row");
  document.getElementById("background")?.classList.add("!hidden");
}
</script>

<template>
  <Head title="Benjamin" />

  <div class="container text-white">
    <div class="row">
      <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
        <Link
          v-if="$page.props.auth.user"
          :href="route('dashboard')"
          class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-yellow/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-yellow/80 dark:focus-visible:ring-white"
        >
          Dashboard
        </Link>

        <template v-else>
          <Link
            :href="route('login')"
            class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-yellow/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-yellow/80 dark:focus-visible:ring-white"
          >
            Log in
          </Link>

          <!-- <Link
            v-if="canRegister"
            :href="route('register')"
            class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-yellow/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-yellow/80 dark:focus-visible:ring-white"
          >
            Register
          </Link> -->
        </template>
      </nav>
    </div>
    <div class="row h-full">
      <div class="col-lg-6 flex items-center justify-center">
        <div class="img-container">
          <img src="/images/norvin-thug.PNG" alt="Benjamin" />
        </div>
      </div>
      <div class="col-lg-6 flex items-center">
        <div>
          <h1>Benjamin</h1>
          <p class="superhero">ANG SUPERHERO NA PWEDENG ARKILAHIN</p>
        </div>
      </div>
    </div>
  </div>
  <div class="black-bg"></div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Share+Tech&display=swap");
h1 {
  text-transform: uppercase;
  font-weight: 700;
  font-size: 90px;
}

.img-container {
  height: 300px;
  width: 300px;
  background-color: gray;
}

.img-container img {
  object-fit: cover;
  width: 100%;
  height: 100%;
}

.container {
  padding: 20px;
  position: relative;
  z-index: 10;
  font-family: "Montserrat", serif;
  height: 100%;
  height: 100vh;
}

.black-bg {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  background-color: black;
}

.superhero {
  opacity: 0.3;
  letter-spacing: 5px;
  font-size: 10px;
  font-weight: 600;
  margin-left: 8px;
}
</style>

