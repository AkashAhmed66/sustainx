<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800">
            {{ __('Create Question') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6">
        <div class="flex flex-col lg:flex-row gap-6"
             x-data="{
                          questionType: '{{ old('question_type_id', '') }}',
                          TYPE_NUMERIC: '{{ \App\Http\Controllers\QuestionController::TYPE_NUMERIC }}',
                          TYPE_MCQ: '{{ \App\Http\Controllers\QuestionController::TYPE_MCQ }}',
                          TYPE_MULTIPLE_SELECT: '{{ \App\Http\Controllers\QuestionController::TYPE_MULTIPLE_SELECT }}',
                          TYPE_MULTIPLE_NUMERIC: '{{ \App\Http\Controllers\QuestionController::TYPE_MULTIPLE_NUMERIC }}',
                          selectedSubsectionId: '{{ old('subsection_id', '') }}',
                          triggerQuestions: {{ $triggerQuestionsJson }},
                          triggerQuestionId: '{{ old('depends_on_question_id', '') }}',
                          triggerOptionId: '{{ old('depends_on_option_id', '') }}',
                          options: {{ old('options') ? json_encode(old('options')) : $defaultOptions }},
                          connectionQuestionsByOption: {{ old('connection_questions') ? json_encode(array_values(old('connection_questions'))) : '[]' }},
                          factors: {{ old('factors') ? json_encode(old('factors')) : $defaultFactors }},
                          equationName: '{{ old('equation_name', '') }}',
                          childQuestions: {{ old('child_questions') ? json_encode(array_values(old('child_questions'))) : $defaultChildQuestions }},
                          get filteredTriggerQuestions() {
                              if (!this.selectedSubsectionId) {
                                  return [];
                              }

                              return this.triggerQuestions.filter((question) => String(question.subsection_id) === String(this.selectedSubsectionId));
                          },
                          get selectedTriggerQuestion() {
                              return this.filteredTriggerQuestions.find((question) => String(question.id) === String(this.triggerQuestionId)) || null;
                          },
                          get availableTriggerOptions() {
                              return this.selectedTriggerQuestion ? this.selectedTriggerQuestion.options : [];
                          },
                          syncDependencySelection() {
                              const stillValidQuestion = this.filteredTriggerQuestions.some((question) => String(question.id) === String(this.triggerQuestionId));
                              if (!stillValidQuestion) {
                                  this.triggerQuestionId = '';
                                  this.triggerOptionId = '';
                                  return;
                              }

                              const stillValidOption = this.availableTriggerOptions.some((option) => String(option.id) === String(this.triggerOptionId));
                              if (!stillValidOption) {
                                  this.triggerOptionId = '';
                              }
                          },
                          clearDependency() {
                              this.triggerQuestionId = '';
                              this.triggerOptionId = '';
                          },
                          isConnectionBuilderVisible() {
                              return this.questionType == '2' || this.questionType == '3';
                          },
                          syncConnectionBuckets() {
                              while (this.connectionQuestionsByOption.length < this.options.length) {
                                  this.connectionQuestionsByOption.push([]);
                              }

                              if (this.connectionQuestionsByOption.length > this.options.length) {
                                  this.connectionQuestionsByOption.splice(this.options.length);
                              }
                          },
                          addOption() {
                              this.options.push({
                                  option_text: '',
                                  option_value: '',
                                  order_no: this.options.length + 1
                              });
                              this.connectionQuestionsByOption.push([]);
                          },
                          removeOption(index) {
                              if (this.options.length > 1) {
                                  this.options.splice(index, 1);
                                  this.connectionQuestionsByOption.splice(index, 1);
                                  // Reorder
                                  this.options.forEach((opt, idx) => opt.order_no = idx + 1);
                              }
                          },
                          addConnectionQuestion(optionIndex) {
                              if (!this.connectionQuestionsByOption[optionIndex]) {
                                  this.connectionQuestionsByOption[optionIndex] = [];
                              }

                              this.connectionQuestionsByOption[optionIndex].push({
                                  id: null,
                                  question_type_id: this.TYPE_NUMERIC,
                                  sl_no: '',
                                  question_text: '',
                                  input_unit: '',
                                  output_unit: '',
                                  options: [],
                                  child_questions: [],
                                  equation_name: '',
                                  factors: [],
                                  is_required: false,
                                  is_active: true
                              });
                          },
                          removeConnectionQuestion(optionIndex, connectionIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];

                              if (bucket.length > 0) {
                                  bucket.splice(connectionIndex, 1);
                              }
                          },
                          // Connection-specific helpers
                          addConnectionOption(optionIndex, connectionIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex];
                              if (!connection.options) connection.options = [];
                              connection.options.push({ option_text: '', option_value: '', order_no: connection.options.length + 1 });
                          },
                          removeConnectionOption(optionIndex, connectionIndex, optIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex] || null;
                              if (!connection || !connection.options) return;
                              connection.options.splice(optIndex, 1);
                          },
                          addConnectionChildQuestion(optionIndex, connectionIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex];
                              if (!connection.child_questions) connection.child_questions = [];
                              connection.child_questions.push({ id: null, question_text: '', input_unit: '', equation_name: '', factors: [{ factor_value: '' }] });
                          },
                          removeConnectionChildQuestion(optionIndex, connectionIndex, childIdx) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex] || null;
                              if (!connection || !connection.child_questions) return;
                              if (connection.child_questions.length > 0) connection.child_questions.splice(childIdx, 1);
                          },
                          addConnectionChildFactor(optionIndex, connectionIndex, childIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex] || null;
                              if (!connection) return;
                              const child = connection.child_questions[childIndex];
                              if (!child) return;
                              if (!child.factors) child.factors = [];
                              child.factors.push({ factor_value: '' });
                          },
                          removeConnectionChildFactor(optionIndex, connectionIndex, childIndex, factorIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex] || null;
                              if (!connection) return;
                              const child = connection.child_questions[childIndex];
                              if (!child || !child.factors) return;
                              if (child.factors.length > 0) child.factors.splice(factorIndex, 1);
                          },
                          addConnectionFactor(optionIndex, connectionIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex];
                              if (!connection) return;
                              if (!connection.factors) connection.factors = [];
                              connection.factors.push({ factor_value: '' });
                          },
                          removeConnectionFactor(optionIndex, connectionIndex, factorIndex) {
                              const bucket = this.connectionQuestionsByOption[optionIndex] || [];
                              const connection = bucket[connectionIndex] || null;
                              if (!connection || !connection.factors) return;
                              if (connection.factors.length > 0) connection.factors.splice(factorIndex, 1);
                          },
                          // Modal helpers for add/edit connection
                          getEmptyConnection() {
                              return {
                                  id: null,
                                  question_type_id: this.TYPE_NUMERIC,
                                  sl_no: '',
                                  question_text: '',
                                  input_unit: '',
                                  output_unit: '',
                                  options: [],
                                  child_questions: [],
                                  equation_name: '',
                                  factors: [],
                                  is_required: false,
                                  is_active: true
                              };
                          },
                          openAddConnectionModal(optionIndex) {
                              this.modalOptionIndex = optionIndex;
                              this.modalConnectionIndex = null;
                              this.modalConnection = this.getEmptyConnection();
                              this.modalOpen = true;
                          },
                          openEditConnectionModal(optionIndex, connectionIndex) {
                              this.modalOptionIndex = optionIndex;
                              this.modalConnectionIndex = connectionIndex;
                              const conn = (this.connectionQuestionsByOption[optionIndex] || [])[connectionIndex] || this.getEmptyConnection();
                              this.modalConnection = JSON.parse(JSON.stringify(conn));
                              this.modalOpen = true;
                          },
                          closeModal() { this.modalOpen = false; this.modalConnection = this.getEmptyConnection(); this.modalOptionIndex = null; this.modalConnectionIndex = null; },
                          saveModalConnection() {
                              if (this.modalConnection.question_text.trim() === '') {
                                  alert('Question text is required');
                                  return;
                              }
                              const optIdx = this.modalOptionIndex;
                              if (!this.connectionQuestionsByOption[optIdx]) this.connectionQuestionsByOption[optIdx] = [];
                              if (this.modalConnectionIndex === null) {
                                  this.connectionQuestionsByOption[optIdx].push(JSON.parse(JSON.stringify(this.modalConnection)));
                              } else {
                                  this.connectionQuestionsByOption[optIdx].splice(this.modalConnectionIndex, 1, JSON.parse(JSON.stringify(this.modalConnection)));
                              }
                              this.closeModal();
                          },
                          // Modal state
                          modalOpen: false,
                          modalOptionIndex: null,
                          modalConnectionIndex: null,
                          modalConnection: {
                              id: null,
                              question_type_id: '',
                              sl_no: '',
                              question_text: '',
                              input_unit: '',
                              output_unit: '',
                              options: [],
                              child_questions: [],
                              equation_name: '',
                              factors: [],
                              is_required: false,
                              is_active: true
                          },
                          addFactor() {
                              this.factors.push({
                                  sn: this.factors.length + 1,
                                  operation: 'multiply',
                                  factor_value: '',
                                  country_id: ''
                              });
                          },
                          removeFactor(index) {
                              if (this.factors.length > 1) {
                                  this.factors.splice(index, 1);
                                  // Reorder
                                  this.factors.forEach((fac, idx) => fac.sn = idx + 1);
                              }
                          },
                          addModalFactor() {
                              if (!this.modalConnection.factors) this.modalConnection.factors = [];
                              this.modalConnection.factors.push({
                                  sn: this.modalConnection.factors.length + 1,
                                  operation: 'multiply',
                                  factor_value: '',
                                  country_id: ''
                              });
                          },
                          removeModalFactor(index) {
                              if (this.modalConnection.factors.length > 1) {
                                  this.modalConnection.factors.splice(index, 1);
                                  this.modalConnection.factors.forEach((f, idx) => f.sn = idx + 1);
                              }
                          },
                          addModalOption() {
                              if (!this.modalConnection.options) this.modalConnection.options = [];
                              this.modalConnection.options.push({
                                  order_no: this.modalConnection.options.length + 1,
                                  option_text: '',
                                  option_value: ''
                              });
                          },
                          removeModalOption(index) {
                              if (this.modalConnection.options.length > 0) {
                                  this.modalConnection.options.splice(index, 1);
                              }
                          },
                          addModalChildQuestion() {
                              if (!this.modalConnection.child_questions) this.modalConnection.child_questions = [];
                              this.modalConnection.child_questions.push({
                                  id: null,
                                  question_text: '',
                                  input_unit: '',
                                  equation_name: '',
                                  factors: [{ sn: 1, operation: 'multiply', factor_value: '', country_id: '' }]
                              });
                          },
                          removeModalChildQuestion(index) {
                              if (this.modalConnection.child_questions.length > 1) {
                                  this.modalConnection.child_questions.splice(index, 1);
                              }
                          },
                          addModalChildFactor(childIndex) {
                              if (!this.modalConnection.child_questions[childIndex].factors) {
                                  this.modalConnection.child_questions[childIndex].factors = [];
                              }
                              const factors = this.modalConnection.child_questions[childIndex].factors;
                              factors.push({
                                  sn: factors.length + 1,
                                  operation: 'multiply',
                                  factor_value: '',
                                  country_id: ''
                              });
                          },
                          removeModalChildFactor(childIndex, factorIndex) {
                              const factors = this.modalConnection.child_questions[childIndex].factors || [];
                              if (factors.length > 1) {
                                  factors.splice(factorIndex, 1);
                                  factors.forEach((f, idx) => f.sn = idx + 1);
                              }
                          },
                          addChildQuestion() {
                              this.childQuestions.push({
                                  id: null,
                                  question_text: '',
                                  input_unit: '',
                                  equation_name: '',
                                  factors: [{ sn: 1, operation: 'multiply', factor_value: '', country_id: '' }]
                              });
                          },
                          removeChildQuestion(index) {
                              if (this.childQuestions.length > 1) {
                                  this.childQuestions.splice(index, 1);
                              }
                          },
                          addChildFactor(childIndex) {
                              if (!this.childQuestions[childIndex].factors) {
                                  this.childQuestions[childIndex].factors = [];
                              }

                              this.childQuestions[childIndex].factors.push({
                                  sn: this.childQuestions[childIndex].factors.length + 1,
                                  operation: 'multiply',
                                  factor_value: '',
                                  country_id: ''
                              });
                          },
                          removeChildFactor(childIndex, factorIndex) {
                              const factors = this.childQuestions[childIndex].factors || [];
                              if (factors.length > 1) {
                                  factors.splice(factorIndex, 1);
                                  factors.forEach((factor, idx) => factor.sn = idx + 1);
                              }
                          }
                      }"
            <!-- Left Dashboard Card: Form Fields -->
            <div class="w-full lg:w-1/2 dashboard-card">
                <div class="p-6">
                    <form id="question-create-form" action="{{ route('questions.store') }}" method="POST"
                      x-effect="syncDependencySelection(); syncConnectionBuckets(); if (questionType == '4' && childQuestions.length === 0) { addChildQuestion(); }">
                        @csrf

                        <div class="space-y-6">
                        <!-- Subsection -->
                        <div>
                            <label for="subsection_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Subsection <span class="text-red-500">*</span>
                            </label>
                            <select name="subsection_id"
                                    id="subsection_id"
                                    x-model="selectedSubsectionId"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('subsection_id') border-red-500 @enderror">
                                <option value="">Select Subsection</option>
                                @foreach($subsections as $subsection)
                                    <option value="{{ $subsection->id }}" {{ old('subsection_id') == $subsection->id ? 'selected' : '' }}>
                                        {{ $subsection->section->name }} → {{ $subsection->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subsection_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Question Text -->
                        <div>
                            <label for="sl_no" class="block text-sm font-medium text-neutral-700 mb-2">
                                Sl No <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                name="sl_no"
                                id="sl_no"
                                value="{{ old('sl_no') }}"
                                required
                                class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('sl_no') border-red-500 @enderror"
                                placeholder="Enter global serial number">
                            @error('sl_no')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Question Text -->
                        <div>
                            <label for="question_text" class="block text-sm font-medium text-neutral-700 mb-2">
                                Question Text <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question_text"
                                      id="question_text"
                                      rows="4"
                                      required
                                      class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('question_text') border-red-500 @enderror"
                                      placeholder="Enter question text">{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Question Type -->
                        <div>
                            <label for="question_type_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                Question Type <span class="text-red-500">*</span>
                            </label>
                            <select name="question_type_id"
                                    id="question_type_id"
                                    x-model="questionType"
                                    required
                                    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('question_type_id') border-red-500 @enderror">
                                <option value="">Select Type</option>
                                @foreach($questionTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('question_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $type->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('question_type_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Main Question Marker -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_main_question"
                                       value="1"
                                       {{ old('is_main_question', false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Use as main question for subsection numeric/comparison dashboards</span>
                            </label>
                        </div>

                        <!-- Input Unit -->
                        <div x-show="questionType != '4'">
                            <label for="input_unit" class="block text-sm font-medium text-neutral-700 mb-2">
                                Input Unit <span class="text-neutral-500 text-xs">(Unit shown during data entry - e.g., MWh, %, kg)</span>
                            </label>
                            <input type="text"
                                   name="input_unit"
                                   id="input_unit"
                                   value="{{ old('input_unit') }}"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('input_unit') border-red-500 @enderror"
                                   placeholder="Optional">
                            @error('input_unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Output Unit -->
                        <div>
                            <label for="output_unit" class="block text-sm font-medium text-neutral-700 mb-2">
                                Output Unit <span class="text-neutral-500 text-xs">(Unit shown in reports/dashboard - e.g., tonnes, kg CO2e)</span>
                            </label>
                            <input type="text"
                                   name="output_unit"
                                   id="output_unit"
                                   value="{{ old('output_unit') }}"
                                   class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('output_unit') border-red-500 @enderror"
                                   placeholder="Optional">
                            @error('output_unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Multiple Numeric Child Questions -->
                        <div x-show="questionType == '4'">
                            <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                <div class="mb-4">
                                    <h3 class="font-medium text-neutral-800">Child Questions (Numeric)</h3>
                                </div>

                                @error('child_questions')
                                    <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="space-y-4">
                                    <template x-for="(child, childIndex) in childQuestions" :key="childIndex">
                                        <div class="border border-neutral-200 rounded-lg p-4 bg-white">
                                            <div class="flex items-center justify-between mb-3">
                                                <p class="text-sm font-semibold text-neutral-700" x-text="`Child Question ${childIndex + 1}`"></p>
                                                <button type="button"
                                                        @click="removeChildQuestion(childIndex)"
                                                        class="text-xs px-2 py-1 rounded bg-red-50 text-red-700 hover:bg-red-100 transition-colors"
                                                        x-show="childQuestions.length > 1">
                                                    Remove
                                                </button>
                                            </div>

                                            <input type="hidden" :name="`child_questions[${childIndex}][id]`" x-model="child.id">

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-neutral-700 mb-2">Child Question Text <span class="text-red-500">*</span></label>
                                                    <input type="text"
                                                           :name="`child_questions[${childIndex}][question_text]`"
                                                           x-model="child.question_text"
                                                           class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                           placeholder="Enter child question text">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-neutral-700 mb-2">Input Unit</label>
                                                    <input type="text"
                                                           :name="`child_questions[${childIndex}][input_unit]`"
                                                           x-model="child.input_unit"
                                                           class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                           placeholder="e.g., kWh, kg, litres">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-neutral-700 mb-2">Equation Name</label>
                                                    <input type="text"
                                                           :name="`child_questions[${childIndex}][equation_name]`"
                                                           x-model="child.equation_name"
                                                           class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                           placeholder="Optional">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-neutral-700 mb-2">Factors</label>

                                                <template x-for="(factor, factorIndex) in child.factors" :key="factorIndex">
                                                    <div class="grid grid-cols-12 gap-3 mb-2 items-start">
                                                        <div class="col-span-1">
                                                            <input type="number"
                                                                   :name="`child_questions[${childIndex}][factors][${factorIndex}][sn]`"
                                                                   x-model="factor.sn"
                                                                   readonly
                                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100">
                                                        </div>
                                                        <div class="col-span-2">
                                                            <select :name="`child_questions[${childIndex}][factors][${factorIndex}][operation]`"
                                                                    x-model="factor.operation"
                                                                    class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                <option value="multiply">×</option>
                                                                <option value="add">+</option>
                                                                <option value="subtract">-</option>
                                                                <option value="divide">÷</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-span-3">
                                                            <input type="number"
                                                                   step="any"
                                                                   :name="`child_questions[${childIndex}][factors][${factorIndex}][factor_value]`"
                                                                   x-model="factor.factor_value"
                                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                                   placeholder="Value">
                                                        </div>
                                                        <div class="col-span-5">
                                                            <select :name="`child_questions[${childIndex}][factors][${factorIndex}][country_id]`"
                                                                    x-model="factor.country_id"
                                                                    class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                <option value="">Select Country (Optional)</option>
                                                                @foreach($countries as $country)
                                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-span-1">
                                                            <button type="button"
                                                                    @click="removeChildFactor(childIndex, factorIndex)"
                                                                    class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                                    title="Remove Factor">
                                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>

                                                <button type="button"
                                                        @click="addChildFactor(childIndex)"
                                                        class="mt-2 px-3 py-1.5 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                                    + Add Factor
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <button type="button"
                                            @click="addChildQuestion()"
                                            class="px-3 py-1.5 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                        + Add Child Question
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Equation Section (for Numeric type) -->
                        <div x-show="questionType == '1'">
                            <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                <h3 class="font-medium text-neutral-800 mb-4">Equation & Factors</h3>
                                
                                <!-- Equation Name -->
                                <div class="mb-4">
                                    <label for="equation_name" class="block text-sm font-medium text-neutral-700 mb-2">
                                        Equation Name
                                    </label>
                                    <input type="text"
                                           name="equation_name"
                                           id="equation_name"
                                           x-model="equationName"
                                           class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                           placeholder="e.g., Carbon Emission Calculation">
                                </div>

                                <!-- Factors -->
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-3">
                                        Factors
                                    </label>
                                    
                                    <template x-for="(factor, index) in factors" :key="index">
                                        <div class="grid grid-cols-12 gap-3 mb-3 items-start">
                                            <!-- SN -->
                                            <div class="col-span-1">
                                                <input type="number"
                                                       :name="'factors[' + index + '][sn]'"
                                                       x-model="factor.sn"
                                                       readonly
                                                       class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100"
                                                       placeholder="SN">
                                            </div>

                                            <!-- Operation -->
                                            <div class="col-span-2">
                                                <select :name="'factors[' + index + '][operation]'"
                                                        x-model="factor.operation"
                                                        class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                    <option value="multiply">×</option>
                                                    <option value="add">+</option>
                                                    <option value="subtract">-</option>
                                                    <option value="divide">÷</option>
                                                </select>
                                            </div>

                                            <!-- Factor Value -->
                                            <div class="col-span-3">
                                                <input type="number"
                                                       :name="'factors[' + index + '][factor_value]'"
                                                       x-model="factor.factor_value"
                                                       step="any"
                                                       class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                       placeholder="Value">
                                            </div>

                                            <!-- Country -->
                                            <div class="col-span-5">
                                                <select :name="'factors[' + index + '][country_id]'"
                                                        x-model="factor.country_id"
                                                        class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                    <option value="">Select Country (Optional)</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="col-span-1">
                                                <button type="button"
                                                        @click="removeFactor(index)"
                                                        class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Remove Factor">
                                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <button type="button"
                                            @click="addFactor()"
                                            class="mt-2 px-4 py-2 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                        + Add Factor
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Options Section (for MCQ and Multiple Select types) -->
                        <div x-show="questionType == '2' || questionType == '3'">
                            <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                <h3 class="font-medium text-neutral-800 mb-4">
                                    <span x-text="questionType == '3' ? 'Multiple Select Options' : 'Multiple Choice Options'"></span>
                                </h3>
                                
                                <template x-for="(option, index) in options" :key="index">
                                    <div class="grid grid-cols-12 gap-3 mb-3 items-start">
                                        <!-- Order No -->
                                        <div class="col-span-1">
                                            <input type="number"
                                                   :name="'options[' + index + '][order_no]'"
                                                   x-model="option.order_no"
                                                   readonly
                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100"
                                                   placeholder="#">
                                        </div>

                                        <!-- Option Text -->
                                        <div class="col-span-7">
                                            <input type="text"
                                                   :name="'options[' + index + '][option_text]'"
                                                   x-model="option.option_text"
                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                   placeholder="Option Text">
                                        </div>

                                        <!-- Option Value -->
                                        <div class="col-span-3">
                                            <input type="number"
                                                   :name="'options[' + index + '][option_value]'"
                                                   x-model="option.option_value"
                                                   step="any"
                                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                                                   placeholder="Value (Optional)">
                                        </div>

                                        <!-- Remove Button -->
                                        <div class="col-span-1">
                                            <button type="button"
                                                    @click="removeOption(index)"
                                                    class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Remove Option">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <button type="button"
                                        @click="addOption()"
                                        class="mt-2 px-4 py-2 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                    + Add Option
                                </button>
                            </div>
                        </div>

                        <!-- Is Required -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_required"
                                       value="1"
                                       {{ old('is_required', false) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Required</span>
                            </label>
                        </div>

                        <!-- Is Active -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm font-medium text-neutral-700">Active</span>
                            </label>
                        </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t">
                            <a href="{{ route('questions.index') }}"
                               class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="btn-primary">
                                Create Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>

              <!-- Right Dashboard Card: Connected Questions -->
              <div class="w-full lg:w-1/2 dashboard-card"
                 x-show="isConnectionBuilderVisible()"
                 x-cloak>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <h3 class="text-base font-semibold text-neutral-800">Connected Questions</h3>
                                    <p class="mt-1 text-xs text-neutral-500">Add dependent numeric questions under each option. They are saved against the selected option automatically.</p>
                                </div>
                            </div>

                            <template x-for="(option, optionIndex) in options" :key="optionIndex">
                                <div class="mb-4 rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div>
                                            <p class="text-sm font-semibold text-neutral-800" x-text="`Option ${option.order_no}`"></p>
                                            <p class="text-xs text-neutral-500" x-text="option.option_text || 'Unnamed option'"></p>
                                        </div>
                                        <button type="button"
                                            @click="openAddConnectionModal(optionIndex)"
                                                class="inline-flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-2 text-xs font-medium text-primary-700 hover:bg-primary-100 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"></path>
                                            </svg>
                                            Add Connection Question
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-if="(connectionQuestionsByOption[optionIndex] && connectionQuestionsByOption[optionIndex].length)">
                                            <div class="space-y-2">
                                                <template x-for="(connection, connectionIndex) in (connectionQuestionsByOption[optionIndex] || [])" :key="connectionIndex">
                                                    <div class="flex items-center justify-between rounded-xl border border-neutral-200 bg-white p-3">
                                                        <div>
                                                            <p class="text-sm font-semibold text-neutral-700" x-text="connection.question_text"></p>
                                                            <p class="text-xs text-neutral-500" x-text="connection.question_type_id ? (connection.question_type_id == TYPE_NUMERIC ? 'Numeric' : (connection.question_type_id == TYPE_MCQ ? 'MCQ' : (connection.question_type_id == TYPE_MULTIPLE_SELECT ? 'Multiple Select' : (connection.question_type_id == TYPE_MULTIPLE_NUMERIC ? 'Multiple Numeric' : '')))) : ''"></p>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click="openEditConnectionModal(optionIndex, connectionIndex)" class="px-3 py-1 text-sm bg-neutral-100 rounded">Edit</button>
                                                            <button type="button" @click="removeConnectionQuestion(optionIndex, connectionIndex)" class="px-3 py-1 text-sm bg-red-50 text-red-700 rounded">Delete</button>
                                                        </div>
                                                        <!-- Hidden inputs to include connection data in main form submission -->
                                                        <div class="hidden">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][id]`" :value="connection.id">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][sl_no]`" :value="connection.sl_no">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][question_text]`" :value="connection.question_text">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][question_type_id]`" :value="connection.question_type_id">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][input_unit]`" :value="connection.input_unit">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][output_unit]`" :value="connection.output_unit">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][equation_name]`" :value="connection.equation_name">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][is_required]`" :value="connection.is_required ? 1 : 0">
                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][is_active]`" :value="connection.is_active ? 1 : 0">
                                                            <template x-for="(opt, optIndex) in (connection.options || [])" :key="optIndex">
                                                                <div>
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][options][${optIndex}][id]`" :value="opt.id || ''">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][options][${optIndex}][option_text]`" :value="opt.option_text">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][options][${optIndex}][option_value]`" :value="opt.option_value">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][options][${optIndex}][order_no]`" :value="opt.order_no">
                                                                </div>
                                                            </template>
                                                            <template x-for="(child, cIdx) in (connection.child_questions || [])" :key="cIdx">
                                                                <div>
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][id]`" :value="child.id">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][question_text]`" :value="child.question_text">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][input_unit]`" :value="child.input_unit">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][equation_name]`" :value="child.equation_name">
                                                                    <template x-for="(f, fi) in (child.factors || [])" :key="fi">
                                                                        <div>
                                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][factors][${fi}][sn]`" :value="f.sn || fi + 1">
                                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][factors][${fi}][operation]`" :value="f.operation || 'multiply'">
                                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][factors][${fi}][factor_value]`" :value="f.factor_value">
                                                                            <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][child_questions][${cIdx}][factors][${fi}][country_id]`" :value="f.country_id || ''">
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                            <template x-for="(f, fi) in (connection.factors || [])" :key="fi">
                                                                <div>
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][factors][${fi}][sn]`" :value="f.sn || fi + 1">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][factors][${fi}][factor_value]`" :value="f.factor_value">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][factors][${fi}][operation]`" :value="f.operation || 'multiply'">
                                                                    <input form="question-create-form" type="hidden" :name="`connection_questions[${optionIndex}][${connectionIndex}][factors][${fi}][country_id]`" :value="f.country_id || ''">
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!(connectionQuestionsByOption[optionIndex] && connectionQuestionsByOption[optionIndex].length)">
                                            <p class="text-xs text-neutral-500">No connected questions added for this option yet.</p>
                                        </template>
                                    </div>
                            </template>

                            <p class="text-xs text-neutral-500" x-show="!options.length">Add at least one option to start connecting questions.</p>
                            <!-- Modal for Add/Edit Connection Question - Exact copy of left form -->
                            <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                                <div class="absolute inset-0 bg-black/40" @click="closeModal()"></div>
                                <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-auto max-h-[95vh]">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between mb-6 border-b pb-4">
                                            <h3 class="text-lg font-semibold text-neutral-800">Add/Edit Connection Question</h3>
                                            <button type="button" @click="closeModal()" class="text-neutral-500 hover:text-neutral-700">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <div class="space-y-6">
                                            <!-- Sl No -->
                                            <div>
                                                <label class="block text-sm font-medium text-neutral-700 mb-2">Sl No <span class="text-red-500">*</span></label>
                                                <input type="number" min="1" x-model="modalConnection.sl_no" class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Enter global serial number">
                                            </div>

                                            <!-- Question Text -->
                                            <div>
                                                <label class="block text-sm font-medium text-neutral-700 mb-2">Question Text <span class="text-red-500">*</span></label>
                                                <textarea x-model="modalConnection.question_text" rows="4" class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Enter question text"></textarea>
                                            </div>

                                            <!-- Question Type -->
                                            <div>
                                                <label class="block text-sm font-medium text-neutral-700 mb-2">Question Type <span class="text-red-500">*</span></label>
                                                <select x-model="modalConnection.question_type_id" class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                    <option value="">Select Type</option>
                                                    @foreach($questionTypes as $type)
                                                        <option value="{{ $type->id }}">{{ ucwords(str_replace('_', ' ', $type->name)) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Input Unit -->
                                            <div x-show="modalConnection.question_type_id != TYPE_MULTIPLE_NUMERIC">
                                                <label class="block text-sm font-medium text-neutral-700 mb-2">Input Unit <span class="text-neutral-500 text-xs">(Unit shown during data entry - e.g., MWh, %, kg)</span></label>
                                                <input type="text" x-model="modalConnection.input_unit" class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Optional">
                                            </div>

                                            <!-- Output Unit -->
                                            <div>
                                                <label class="block text-sm font-medium text-neutral-700 mb-2">Output Unit <span class="text-neutral-500 text-xs">(Unit shown in reports/dashboard - e.g., tonnes, kg CO2e)</span></label>
                                                <input type="text" x-model="modalConnection.output_unit" class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Optional">
                                            </div>

                                            <!-- Multiple Numeric Child Questions -->
                                            <div x-show="modalConnection.question_type_id == TYPE_MULTIPLE_NUMERIC">
                                                <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                                    <div class="mb-4">
                                                        <h4 class="font-medium text-neutral-800">Child Questions (Numeric)</h4>
                                                    </div>
                                                    <div class="space-y-4">
                                                        <template x-for="(child, childIndex) in (modalConnection.child_questions || [])" :key="childIndex">
                                                            <div class="border border-neutral-200 rounded-lg p-4 bg-white">
                                                                <div class="flex items-center justify-between mb-3">
                                                                    <p class="text-sm font-semibold text-neutral-700" x-text="`Child Question ${childIndex + 1}`"></p>
                                                                    <button type="button" @click="removeModalChildQuestion(childIndex)" class="text-xs px-2 py-1 rounded bg-red-50 text-red-700 hover:bg-red-100 transition-colors" x-show="(modalConnection.child_questions || []).length > 1">
                                                                        Remove
                                                                    </button>
                                                                </div>
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                                    <div class="md:col-span-2">
                                                                        <label class="block text-sm font-medium text-neutral-700 mb-2">Child Question Text <span class="text-red-500">*</span></label>
                                                                        <input type="text" x-model="child.question_text" class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Enter child question text">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-sm font-medium text-neutral-700 mb-2">Input Unit</label>
                                                                        <input type="text" x-model="child.input_unit" class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g., kWh, kg, litres">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-sm font-medium text-neutral-700 mb-2">Equation Name</label>
                                                                        <input type="text" x-model="child.equation_name" class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Optional">
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-sm font-medium text-neutral-700 mb-2">Factors</label>
                                                                    <template x-for="(factor, factorIndex) in (child.factors || [])" :key="factorIndex">
                                                                        <div class="grid grid-cols-12 gap-3 mb-2 items-start">
                                                                            <div class="col-span-1">
                                                                                <input type="number" x-model="factor.sn" readonly class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100">
                                                                            </div>
                                                                            <div class="col-span-2">
                                                                                <select x-model="factor.operation" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                                    <option value="multiply">×</option>
                                                                                    <option value="add">+</option>
                                                                                    <option value="subtract">-</option>
                                                                                    <option value="divide">÷</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-span-3">
                                                                                <input type="number" step="any" x-model="factor.factor_value" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Value">
                                                                            </div>
                                                                            <div class="col-span-5">
                                                                                <select x-model="factor.country_id" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                                    <option value="">Select Country (Optional)</option>
                                                                                    @foreach($countries as $country)
                                                                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-span-1">
                                                                                <button type="button" @click="removeModalChildFactor(childIndex, factorIndex)" class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Remove Factor">
                                                                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </template>
                                                                    <button type="button" @click="addModalChildFactor(childIndex)" class="mt-2 px-3 py-1.5 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                                                        + Add Factor
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <div class="mt-4 flex justify-end">
                                                        <button type="button" @click="addModalChildQuestion()" class="px-3 py-1.5 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                                            + Add Child Question
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Equation Section (for Numeric type) -->
                                            <div x-show="modalConnection.question_type_id == TYPE_NUMERIC">
                                                <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                                    <h4 class="font-medium text-neutral-800 mb-4">Equation & Factors</h4>
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-neutral-700 mb-2">Equation Name</label>
                                                        <input type="text" x-model="modalConnection.equation_name" class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="e.g., Carbon Emission Calculation">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-neutral-700 mb-3">Factors</label>
                                                        <template x-for="(factor, index) in (modalConnection.factors || [])" :key="index">
                                                            <div class="grid grid-cols-12 gap-3 mb-3 items-start">
                                                                <div class="col-span-1">
                                                                    <input type="number" x-model="factor.sn" readonly class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100" placeholder="SN">
                                                                </div>
                                                                <div class="col-span-2">
                                                                    <select x-model="factor.operation" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                        <option value="multiply">×</option>
                                                                        <option value="add">+</option>
                                                                        <option value="subtract">-</option>
                                                                        <option value="divide">÷</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-span-3">
                                                                    <input type="number" step="any" x-model="factor.factor_value" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Value">
                                                                </div>
                                                                <div class="col-span-5">
                                                                    <select x-model="factor.country_id" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                        <option value="">Select Country (Optional)</option>
                                                                        @foreach($countries as $country)
                                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-span-1">
                                                                    <button type="button" @click="removeModalFactor(index)" class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Remove Factor">
                                                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <button type="button" @click="addModalFactor()" class="mt-2 px-4 py-2 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                                            + Add Factor
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Options Section (for MCQ and Multiple Select types) -->
                                            <div x-show="modalConnection.question_type_id == TYPE_MCQ || modalConnection.question_type_id == TYPE_MULTIPLE_SELECT">
                                                <div class="border border-neutral-200 rounded-xl p-4 bg-neutral-50">
                                                    <h4 class="font-medium text-neutral-800 mb-4">
                                                        <span x-text="modalConnection.question_type_id == TYPE_MULTIPLE_SELECT ? 'Multiple Select Options' : 'Multiple Choice Options'"></span>
                                                    </h4>
                                                    <template x-for="(option, index) in (modalConnection.options || [])" :key="index">
                                                        <div class="grid grid-cols-12 gap-3 mb-3 items-start">
                                                            <div class="col-span-1">
                                                                <input type="number" x-model="option.order_no" readonly class="w-full px-3 py-2 border border-neutral-300 rounded-lg bg-neutral-100" placeholder="#">
                                                            </div>
                                                            <div class="col-span-7">
                                                                <input type="text" x-model="option.option_text" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Option Text">
                                                            </div>
                                                            <div class="col-span-3">
                                                                <input type="number" step="any" x-model="option.option_value" class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Value (Optional)">
                                                            </div>
                                                            <div class="col-span-1">
                                                                <button type="button" @click="removeModalOption(index)" class="w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Remove Option">
                                                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <button type="button" @click="addModalOption()" class="mt-2 px-4 py-2 text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors font-medium text-sm">
                                                        + Add Option
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Is Required and Is Active -->
                                            <div class="flex flex-wrap items-center gap-6 pt-4 border-t">
                                                <label class="flex items-center">
                                                    <input type="checkbox" x-model="modalConnection.is_required" class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                                    <span class="ml-2 text-sm font-medium text-neutral-700">Required</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="checkbox" x-model="modalConnection.is_active" class="w-4 h-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500">
                                                    <span class="ml-2 text-sm font-medium text-neutral-700">Active</span>
                                                </label>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                                                <button type="button" @click="closeModal()" class="px-6 py-2.5 text-neutral-700 bg-neutral-100 rounded-lg hover:bg-neutral-200 transition-colors font-medium">
                                                    Cancel
                                                </button>
                                                <button type="button" @click="saveModalConnection()" class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
