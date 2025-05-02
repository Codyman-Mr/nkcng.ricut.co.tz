

    <!-- Modal Backdrop -->
    <div x-data="{ show: @entangle('showModal') }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed z-20 inset-0 flex items-center justify-center">
        <div class="fixed inset-0 bg-gray-500 opacity-40"></div>
        <div class="bg-white rounded-lg shadow-lg p-4 w-11/12 max-w-2xl relative max-h-[90vh]">
            <div class="mb-4" role="document">
                  <div class="text-center">
                <h5 class="font-bold text-xl mb-1">MIKOPO NA TARATIBU ZAKE.</h5>
            </div>

            <div class="modal-body mt-1 overflow-y-scroll max-h-[60vh]">
                <h3 class="font-semibold">AINA ZA MIKOPO</h3>
                <ul class="list-disc list-inside">
                    <li>Mikopo kupitia maendeleo bank</li>
                    <li> Mikopo kupitia Nk cng auto ltd</li>
                </ul>

                <br>
                <h3 class=" font-semibold mb-2">MIKOPO KUPITIA MAENDELEO BANK</h3>
                <h5 class="text-md  font-semibold">VIGEZO NA MASHARTI</h5>
                <ul class="list-disc list-inside">
                    <li>Kadi ya gari original ikiwa na majina kamili ya mkopaji.</li>
                    <li>Nida ya mkopaji na isome sawa saw ana kadi ya gari ya mkopaji.</li>
                    <li>Utambulisho wa makazi kuitia serikali za mtaa.</li>
                    <li>Gari lenye usajili B, C, D, E etc.</li>
                </ul>

                <br>
                <h5 class="font-black">MUDA WA MKOPO</h5>
                <span class="font-medium text-pretty text-md">miezi mi3 hadi 18</span>

                <br><br>
                <h3 class="font-black">MIKOPO KUPITIA NK CNG AUTO LTD</h3>
                <h5 class="font-semibold text-md">VIGEZO NA MASHARTI</h5>
                <ul class="list-disc list-inside">
                    <li>Kadi ya gari original ikiwa na majina kamili ya mkopaji.</li>
                    <li>Nida ya mkopaji na isome sawa saw ana kadi ya gari ya mkopaji.</li>
                    <li>Utambulisho wa makazi kuitia serikali za mtaa.</li>
                    <li>Gari lenye usajili B, C, D, E etc.</li>
                    <li>Wadhamini wawili mmoja lazima awe muajiriwa;hapa anweza kupakia nyaraka zao
                        za utambulisho wa kazi na makazi na nida au leseni au mpiga kura</li>
                </ul>

                <br>
                <h3 class="font-black text-md">MUDA WA MKOPO</h3>
                <span class="text-md ">miezi mi2 hadi mi4</span>

                <br><br>
                <h3 class="font-black mb-1">VYOMBO VYA MOTO VINAVYOKOPESHWA</h3>
                <b>BAJAJI :</b>
                <p>1,600,000/= kianzio ni 200,000/= marejesho ya wiki ni 80,000/=</p>

<br>
                <b>MAGARI:</b>
                <p>Kianzio ni laki 4
                    Magari madogo 1,900,000/=
                    Ist,rumion,spacio,raum etc
                    Yenye cc kati ya 690-1990
                    Magari ya kati
                    Kianzio ni milioni moja mpaka laki 8
                    Cc 1990 – 2500
                    2.2M
                    2.4M
                    2.6M</p>

                <br>
            </div>
            <div class="flex justify-between mt-2">



                <button class="p-2 bg-rose-500 text-white rounded" wire:click="closeModal">
                    Close
                </button>

                <button class="p-2 bg-green-500 text-white rounded" wire:click="accept">
                    Accept
                </button>
            </div>
        </div>
    </div>
