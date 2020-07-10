class CollaboratorPageMapper {
    constructor() {
        if (!CollaboratorPageMapper.instance) {
            this.form = '#collaborator_form';
            this.presentation = '#presentation';
            this.plan = '#plan';
            this.submitBtn = '#collaborator_submit';

            CollaboratorPageMapper.instance = this;
        }

        return  CollaboratorPageMapper.instance;
    }
}

const collaboratorPageMapper = new CollaboratorPageMapper();

Object.freeze(collaboratorPageMapper);

export default collaboratorPageMapper;