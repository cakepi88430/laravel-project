<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <!-- <x-jet-authentication-card-logo /> -->
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
        恭喜您，信箱已經驗證，請點擊以下連結進行cake系統
        </div>

        <div class="mt-12 flex items-center justify-between" style="margin: auto;">
            <div>
                <x-jet-button onclick="location.href = 'http://cake.local.tw';">
                    Cake
                </x-jet-button>
            </div>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
