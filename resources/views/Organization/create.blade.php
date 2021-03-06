@extends('app')

@section('title', trans('title.name'))

@section('content')
<div class="container main-container">
  <div class="row">
    @include('includes.side_bar_menu')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
      @include('includes.response')
      <div class="element-panel-heading">
        <div>
          <span>{{ ($organizations) ? 'Edit Partner Organization' : 'Add a new Partner Organization' }}</span>
        </div>
      </div>
      <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper">
        <div class="panel panel-default">
          <div class="panel-body">
            <style>
              .create-form .btn-submit {
                width: 218px;
              }

              @media (max-width: 480px) {
                .create-form .btn-submit {
                  width: 92%;
                }
              }

              @media (max-width: 820px) {
                .create-form .btn-cancel {
                  left: 233px;
                }
              }

              @media (max-width: 580px) {
                .create-form .btn-cancel {
                  left: -5px;
                  bottom: 25px;
                }
              }
            </style>

            <div class="create-form">
              <div class="loading-div"></div>
              <div v-cloak class="create-form" id="participatingContainer"
                data-organization="{{json_encode($organizations)}}" data-route="{{$formRoute}}">
                <div v-if="display_error" class="alert alert-danger">
                  Please fix the following errors.
                  <div v-if="error">
                    <li>
                      @{{ error }}
                    </li>
                  </div>
                </div>

                {{Form::open()}}
                <participating-org v-for="(organisation,index) in organisations"
                  v-on:search="setCurrentOrganization(index,$event)" :organisation="organisation"
                  v-on:display="displayModal($event)" :display_error="display_error" v-if="organisations" :key="index">
                </participating-org>

                <modal v-show="showModal" v-on:close="closeModal" :organisation="currentOrganisation"
                  :registrar_list="registrarList"></modal>
                <button class="btn btn-submit btn-form" type="button" v-on:click.prevent="onSubmit">
                  Save
                </button>
                <a class="btn btn-cancel" href="{{route('organization.index', $id)}}">Cancel</a>
                {{Form::close()}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/x-template" id="participating-form">
  <div class="collection_form has_add_more">
    <div class="reset-form-option reset-form-option--small pull-right" v-on:click="reset">Reset
    </div>

    <div class="form-group">
      <div class="form-group" v-bind:class="{'has-error': (organisation.name[0]['narrative'] == '' && display_error)}">
          {{Form::label('Organization',trans('elementForm.organisation'),['class' => 'control-label'])}}
          {{Form::text('organization',null,['class' => 'form-control ignore_change','v-bind:value' => 'organisation.name[0]["narrative"]','@focus' => 'displaySuggestion($event)', '@keydown.tab'=> 'hideSuggestion','autocomplete' => 'off', 'readonly' => true])}}

          <div v-if="(organisation.name[0]['narrative'] == '' && display_error)" class="text-danger">
              Organisation Name is required.
          </div>

          <div v-if="display_org_list" class="publisher-wrap">
              <ul class="filter-publishers">
                  <li>
                      <div class="search-publishers">
                          <input type="search" class="keyword" :value="keyword" placeholder="Filter by organisation name..." @keyup='search($event)' @blur='hide($event)'>
                      </div>
                  </li>
              </ul>
              <ul v-if="suggestions.length > 0" class="found-publishers">
                  <li><p class="publisher-description">Choose an organisation from below</p></li>
                  <li class="publishers-list scroll-list">
                      <div v-for="(publisher, index) in suggestions" :key="index">
                          <a href="#" @mousedown.prevent="selected(publisher, index)" v-bind:selectedSuggestion="index" v-if="typeof index === 'number'">
                              <p>
                                  <strong v-bind:selectedSuggestion="index">@{{publisher.names[0].name}}</strong>
                                  <span class="language" v-if="key.language" v-for="(key,index) in publisher.names">@{{ key.language }}</span>
                              </p>
                              <p>
                                  <strong v-bind:selectedSuggestion="index">@{{publisher.identifier}}</strong>
                              </p>
                              <div class="partners" style="overflow: hidden;" v-bind:selectedSuggestion="index">
                                  <div class="pull-left">
                                      <span v-bind:selectedSuggestion="index" class="tick">
                                          @{{publisher.type | getOrganisationType}}, @{{ publisher.country }}
                                      </span>
                                  </div>
                                  {{-- <div class="pull-right">
                                      <a target="_blank" v-bind:href="'{{ env('PO_API_URL') }}' + '/suggestion/' + publisher.identifier + '/suggest'" class="suggest-edit"
                                          v-if="publisher.is_publisher || publisher.is_org_file">@lang('global.suggest')</a>
                                  </div> --}}
                              </div>
                          </a>
                      </div>
                  </li>
                  <li>
                      <p class="publisher-description" style="margin-bottom:5px;padding-bottom:5px;border-bottom: 1px solid #DFEDF2;">The above list is pulled from IATI Registry publisher's
                          list.</p>
                      <p class="publisher-description go-to__org-finder">
                          <strong><a href="#" @mousedown.prevent="display()">Didn't find what you are looking for? Go to <strong>"Organisation Finder"</strong> to search for the organisation
                              you are looking for.
                          </a></strong>
                      </p>
                  </li>
              </ul>
              <ul v-if="display_org_finder" class="not-found-publisher">
                  <li class="publisher-description">
                    <p>It seems there's no matching organisation in IATI Registry of publishers. You may do one of the following at this point.</p>
                  </li>
                  <li id="orgFinder">
                      <a href="#" @mousedown.prevent="display()">
                          <h3 class="contact-heading">Use Organization Finder <span> (org-id.guide)</span></h3>
                          <p>Use our organization finder helper to get a new identifier for this.</p>
                          <p><span class="caution">Caution:</span> Please beware that this can be a long and
                              tedious process. It may be the case that you will not
                              find the organization even with this. In this case, leave the identifier field blank
                              and just mention organisation name only.</p>
                      </a>
                  </li>
              </ul>
          </div>
      </div>
      <div class="form-group" v-bind:class="{'has-error': (organisation.type == '' && display_error)}">
          {{Form::label('type',trans('elementForm.organisation_type'),['class' => 'control-label'])}}
          <vue-select2 :bind_variable='organisation' name='type' attr_name='typeText' options='{{json_encode($organizationTypes)}}' :disable_options='disable_options'></vue-select2>


          <div v-if="(organisation.type == '' && display_error)" class="text-danger">Organisation Type is
              required.
          </div>
      </div>

      <div class="form-group" v-bind:class="{'has-error': (organisation.country == '' && display_error), 'disabled': disable_options}">
          {{Form::label('country','Country the organization is based in',['class' => 'control-label'])}}
          <vue-select2 :bind_variable='organisation' name='country' attr_name='countryText' options='{{json_encode($countries)}}' :disable_options='disable_options'></vue-select2>
          <div v-if="(organisation.country == '' && display_error)" class="text-danger">Country is required.
          </div>
      </div>

      <div class="form-group organisation-identifier">
          {{Form::label('organisation_identifier','Organisation Identifier:',['class' => 'control-label'])}}
          @{{organisation.identifier}}
          <div v-if="(organisation.identifier.match(/[\&\|\?|]+/) && display_error)" class="text-danger">Special characters are not allowed.</div>
      </div>
    </div>
  </div>
</script>

<script type="text/x-template" id="modalComponent">
  <div class="modal fade org-modal" id="myModal" role="dialog">
    <div class="modal-dialog ">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" @click="close(false)">&times;</button>
              <h4 class="modal-title">Add from org-id.guide</h4>
          </div>
          <div class="modal-body">
              <div class="form-group">
                  <label for="type" v-bind:class="[{'text-danger': inputErrors.selectedOrgType}, 'control-label']">Organisation Type *</label>
                  <vue-select2 :bind_variable='organisation' name='type' attr_name='typeText' options='{{json_encode($organizationTypes)}}' v-on:change='getRegistrars($event)'></vue-select2>
              </div>

              <div class="form-group">
                  <!-- {{Form::label('country','Country the organization is based in',['class' => 'control-label'])}} -->
                  <label for="country" v-bind:class="[{'text-danger': inputErrors.selectedOrgCountry}, 'control-label']">Country the organization is based in *</label>

                  <vue-select2 :bind_variable='organisation' name='country' attr_name='countryText' options='{{json_encode($countries)}}' v-on:change='getRegistrars($event)'></vue-select2>
              </div>
              <div class="form-group">
                      {{Form::label('Organisation Name',trans('elementForm.organisation_name'),['class' => 'control-label'])}}
                      {{Form::text('name', null,['class' => 'form-control ignore_change', 'v-bind:value' => 'organisation.tempName', '@blur' => 'updateOrgName($event)'])}}
                  </div>
                  <div class="form-group" v-if="display_registrar_list">
                <label for="use-organisation-number">
                <input type="checkbox" v-model="useOrganisationNumber" id="use-organisation-number" />
                I have organisation registration number for above organisation.
              </label>
              </div>

              <div class="suggestions" v-if="useOrganisationNumber">
                  <p v-bind:class="{'text-danger':inputErrors.selectedOrg}">PLEASE CHOOSE A LIST FROM BELOW *:</p>
                  <div class="lists scroll-list">
                      <ul>
                          <li v-for="(list,index) in registrar_list[0]" v-if="registrar_list[0]" :key="index">
                              <div class="register-list">
                                  <label>
                                      <input type="radio" name="registrar" v-on:change="displayForm($event)"
                                              v-bind:value="list['code']" v-model="selectedOrg" />
                                      <span>@{{ list['name']['en'] }}
                                          <strong>(@{{ list['code'] }})</strong></span>
                                  </label>
                              </div>
                              <div class="score-block"><span>Quality Score: <strong>@{{ list['quality'] }}</strong></span><span>
                                      <a v-bind:href="list.url" target="_blank">View this list →</a></span>
                              </div>
                          </li>
                      </ul>
                  </div>



                      <div class="form-group">
                      <div v-if="selectedOrg">
                          {{Form::label('Identifier','Organisation Registration Number',['class' => 'control-label'])}}
                          {{Form::text('identifier', null,['class' => 'form-control ignore_change', 'v-bind:value' => 'organisation.tempIdentifier', '@blur' => 'updateOrgIdentifier($event)'])}}
                        </div>
                      </div>


              </div>
              <div class="form-group">
                          <button class="btn btn-form" type="button" @click="close(true)">Use this organisation</button>
                      </div>
          </div>
          <div class="modal-footer">
              <div class="reset-form-option">
                  <a @click="resetForm">Reset form</a>
                  <p>Reset the above form to start again.</p>
              </div>
          </div>
        </div>
    </div>
  </div>
</script>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>
<script src="https://unpkg.com/vue/dist/vue.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/vue/vue-select2.js')}}"></script>
<script>
  var apiUrl = "{!! env('PO_API_URL') !!}";
      var countries = {!! json_encode($countries) !!};
      var types = {!! json_encode($organizationTypes) !!};
</script>

<script>
  Vue.component('participating-org', {
        template: '#participating-form',
        data: function () {
          return {
            display_org_finder: false,
            disable_options: false,
            display_org_list: false,
            suggestions: [],
            searching: false,
            keyword: '',
            countries: [],
            types: []
          }
        },
        updated: function () {
          $('.scroll-list').jScrollPane({ autoReinitialise: true });
          if (this.display_org_list === true) {
            var container = this.$el;
            $(container).find('.keyword').focus();
          }
          if (!this.organisation.is_publisher) {
            this.disable_options = false;
          }
        },
        mounted: function () {
          this.countries = countries;
          this.types = types;
          if (this.organisation.is_publisher) {
            this.disable_options = true;
          }
        },
        props: ['organisation', 'display_error'],
        methods: {
          displaySuggestion: function (event) {
            this.display_org_list = true;
            this.keyword = '';
          },
          search: function (event) {
            if (event.keyCode === 27) {
              this.display_org_list = false;
            }

            var self = this;
            this.keyword = event.target.value;
            if (event.target.value.trim().length > 3) {
              if (!self.searching) {
                self.searching = true;
                this.suggestions = [];
                setTimeout(function () {
                  axios({
                    method: 'GET',
                    url: '/get-org-data/'+event.target.value
                  }).then(function (response) {
                    self.searching = false;
                    self.display_org_finder = false;

                    response.data.forEach(function (publisher) {
                      publisher.is_publisher = true;
                      self.suggestions.push(publisher);
                    });
                  }).catch(function (error) {
                    self.suggestions = [];
                    self.display_org_finder = true;
                    self.searching = false;
                  });
                }, 1000);
              }
            } else {
              this.suggestions = [];
              this.display_org_finder = false;
            }
            this.$emit('search', this.index);
          },
          hideSuggestion: function () {
            this.display_org_finder = false;
            this.display_org_list = false;
            this.suggestions = [];
          },
          onchange: function (event) {
            this.organisation[event.target.name] = event.target.value;
          },
          display: function () {
            this.display_org_finder = false;
            this.display_org_list = false;
            var country = this.organisation['country'];
            var type = this.organisation['type'];
            this.organisation.tempName = this.keyword;
            this.organisation.tempIdentifier = '';
            var self = this;

            if (country != "" || type != "") {
              axios.get('/findorg?country=' + country)
                .then(function (response) {
                  self.$emit('display', response.data);
                });
            } else {
              self.$emit('display', []);
            }
          },
          selected: function (publisher, index) {

            var organizationCountry = publisher['country'];

            this.organisation['type'] = publisher['type'];
            this.organisation['is_publisher'] = publisher['is_publisher'];
            this.organisation['identifier'] = publisher['identifier'].replace(/\//g, "-");
            this.organisation['country'] = organizationCountry ? organizationCountry : "";
            this.organisation['countryText'] = this.countries[organizationCountry];
            this.organisation['typeText'] = this.types[this.organisation['type']];

            var self = this;

            if (publisher['names'].length > 0) {
              publisher['names'].forEach(function (name, index) {
                if (self.organisation['name'][index] == undefined) {
                  self.organisation['name'][index] = { 'narrative': '', 'language': '' };
                }
                self.organisation['name'][index]['narrative'] = name.name;
                self.organisation['name'][index]['language'] = name.language;
              });
            }
            this.disable_options = true;
            this.display_org_list = false;
            this.suggestions = [];
          },
          hide: function (event) {
            if (!event.relatedTarget) {
              this.display_org_finder = false;
              this.display_org_list = false;
              this.suggestions = [];
            }
          },
          reset: function () {
            this.organisation['type'] = '';
            this.organisation['is_publisher'] = '';
            this.organisation['identifier'] = '';
            this.organisation['name'][0]['narrative'] = '';
            this.organisation['country'] = '';
            this.organisation['countryText'] = '';
            this.organisation['typeText'] = '';
            this.organisation['name'][0]['language'] = '';
          }
        }
      });

      Vue.filter('getOrganisationType', function (value) {
        if (value && (types[value] !== undefined)) {
          return types[value];
        }

        return '';
      });

      Vue.component('modal', {
        template: '#modalComponent',
        props: ['organisation', 'registrar_list'],
        data: function () {
          return {
            display_org_info_form: false,
            selectedRegistrar: '',
            useOrganisationNumber: false,
            selectedOrg: '',
            inputErrors: {
              selectedOrgType: 0,
              selectedOrgCountry: 0,
              selectedOrg: 0
            }
          }
        },
        computed: {
          display_registrar_list: function () {
            if (this.registrar_list[0] != undefined) {
              if (this.registrar_list[0].length != 0) {
                return true;
              }
            }

            return false;
          }
        },
        updated: function () {
          if (this.display_registrar_list) {
            var element = this.$el;
            setTimeout(function () {
              var scroll = $(element).find('.scroll-list');
              scroll.jScrollPane({ autoReinitialise: true });
            }, 900);
          }
        },
        methods: {
          close: function (bind) {
            if (bind) {
              if(this.useOrganisationNumber && !this.selectedOrg) {
                this.inputErrors.selectedOrg = 1;
              } else {
                this.inputErrors.selectedOrg = 0;
              }
              if(!this.organisation['type']) {
                this.inputErrors.selectedOrgType = 1;
              }
              if(!this.organisation['country']) {
                this.inputErrors.selectedOrgCountry = 1;
              }
              var inputErrors = this.inputErrors;
              var errorValues = Object.keys(inputErrors).map(function(e) {
                return inputErrors[e];
              });
              var isError = errorValues.filter(function(v) {
                return v === 1;
              }).length;
              if(isError) {
                return false;
              }

              this.selectedOrg = '';
              this.useOrganisationNumber = false;
              this.display_org_info_form = false;
              this.selectedRegistrar = '';
              this.organisation['name'][0]['narrative'] = this.organisation.tempName;
              this.organisation['name'][0]['language'] = '';
              this.organisation['identifier'] = this.organisation.tempIdentifier;
              this.organisation['is_publisher'] = false;
              delete this.organisation.tempName;
              delete this.organisation.tempIdentifier;
            }

            this.$emit('close', false);
          },
          displayForm: function (event) {
            this.selectedRegistrar = event.target.getAttribute('value');
            this.display_org_info_form = true;
            if(this.selectedRegistrar) {
              this.inputErrors.selectedOrg = 0;
            }
          },
          getRegistrars: function (event) {
            var self = this;
            this.organisation[event.name] = event.value;
            var country = this.organisation['country'];
            var type = this.organisation['type'];
            if(country) {
              this.inputErrors.selectedOrgCountry = 0;
            }
            if(type) {
              this.inputErrors.selectedOrgType = 0;
            }

            if (country != "") {
              axios.get('/findorg?country=' + country)
                .then(function (response) {
                  self.registrar_list.splice(0, self.registrar_list.length);
                  self.registrar_list.push(response.data);
                });
            }
          },
          updateOrgName: function (event) {
            if (event.target.value) {
              this.organisation.tempName = event.target.value;
            }
          },
          updateOrgIdentifier: function (event) {
            if (event.target.value) {
              this.organisation.tempIdentifier = this.selectedRegistrar + '-' + event.target.value;
            }
          },
          resetForm: function () {
            this.defaultData();
          },
          defaultData: function () {
            this.organisation['type'] = '';
            this.organisation['is_publisher'] = '';
            this.organisation['identifier'] = '';
            this.organisation['name'][0]['narrative'] = '';
            this.organisation['country'] = '';
            this.organisation['name'][0]['language'] = '';
            this.organisation['countryText'] = '';
            this.organisation['typeText'] = '';
            this.inputErrors = {
              selectedOrgType: 0,
              selectedOrgCountry: 0,
              selectedOrg: 0
            };
            this.selectedOrg = '';
            this.useOrganisationNumber = false;
            this.display_org_info_form = false;
          }
        }
      });

      new Vue({
        el: '#participatingContainer',
        data: {
          organisations: [],
          showModal: false,
          currentOrganisation: [],
          registrarList: [],
          display_error: false,
          error: ''
        },
        mounted: function () {
          $("div.loading-div").hide();
          if (JSON.parse(this.$el.getAttribute('data-organization'))) {
            this.organisations = JSON.parse(this.$el.getAttribute('data-organization'));
          } else {
            this.organisations.push({
              "identifier": "",
              "type": "",
              "country": "",
              "name": [{ "language": "", "narrative": "" }],
              "is_publisher": false
            });
          }
        },
        methods: {
          setCurrentOrganization: function (index) {
            this.currentOrganisation = this.organisations[index];
          },
          displayModal: function (event) {
            this.registrarList.splice(0, this.registrarList.length);
            if (event) {
              this.registrarList.push(event);
            }
            this.showModal = true;
            $('#myModal').modal('show');
          },
          addOrganisations: function () {
            this.organisations.push({
              "identifier": "",
              "type": "",
              "country": "",
              "name": [{ "narrative": "" }]
            });
          },
          closeModal: function () {
            this.showModal = false;
            $('#myModal').modal('hide');
          },
          onSubmit: function () {
            var route = this.$el.getAttribute('data-route');
            var self = this;
            if (this.isValid()) {
              axios.post(route, { organisation: self.organisations })
                .then(function (response) {
                  window.location.href = '/organization/';
                }).catch(function (error) {
                  if (error.response.status == 400) {
                    self.error = error.response.data;
                    self.display_error = true;
                  }
              });
            }
          },
          isValid: function () {
            var organisation = this.organisations[0];
            if (organisation.hasOwnProperty('typeText')) {
              delete organisation.typeText;
            }

            if (organisation.hasOwnProperty('countryText')) {
              delete organisation.countryText;
            }
            if ((organisation.type === '') || (organisation.name[0]['narrative'] === '') || (organisation.country === '') || organisation.identifier.match(/[\&\|\?|]+/)) {
              this.display_error = true;
              return false;
            }

            this.display_error = false;
            return true;
          }
        }
      });
</script>
@endsection