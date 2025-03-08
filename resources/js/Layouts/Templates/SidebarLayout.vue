<template>
    <div class="mx-auto grid grid-cols-12 gap-8">
        <main class="col-span-9">
            <div class="bg-white shadow rounded-lg overflow-hidden p-6">
                <slot />
            </div>
        </main>

        <aside class="col-span-3">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Navigation
                </h2>
                <div class="space-y-2">
                    <slot name="sidebar">
                        <p class="text-sm text-gray-600">
                            Cette barre latérale sera personnalisable dans une future mise à jour.
                        </p>
                    </slot>
                </div>
            </div>
        </aside>
    </div>
</template>